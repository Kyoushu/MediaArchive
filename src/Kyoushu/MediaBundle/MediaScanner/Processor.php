<?php

namespace Kyoushu\MediaBundle\MediaScanner;

use Doctrine\ORM\EntityManager;
use Kyoushu\MediaBundle\Entity\Media;
use Kyoushu\MediaBundle\Entity\TvShow;
use Kyoushu\MediaBundle\Entity\TvShowAlias;
use Moinax\TvDb\CurlException;

use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Coordinate\TimeCode;

use Moinax\TvDb\Client;

class Processor{
    
    private $entityManager;
    private $screencapRootDir;
    private $screencapOffset;
    private $webRootDir;
    private $tvdbClient;
    private $tvdbBaseUrl;
    
    const REGEX_EPISODE_INFO = '/^(?P<tvShowName>.+)( \- |\.)((?P<airDate>[0-9]{4}\-[0-9]{2}\-[0-9]{2})|S(?P<seasonNumberA>[0-9]+)E(?P<episodeNumberA>[0-9]+)|((?P<seasonNumberB>[1-9]([0-9]+)?)x(?P<episodeNumberB>[0-9]+)))/i';
    const REGEX_MOVIE_INFO = '/^(?P<movieName>[^(]+)\(?(?P<year>(1|2)[0-9]{3})\)?/';
    const REGEX_STRIP_LEADING_ZEROES = '/^0+/';    
    
    const GROUP_DIR_DIVISION = 500;
    
    public function __construct(EntityManager $entityManager, Client $tvdbClient, $tvdbBaseUrl){
        $this->entityManager = $entityManager;
        $this->tvdbClient = $tvdbClient;
        $this->tvdbBaseUrl = $tvdbBaseUrl;
        $this->screencapRootDir = null;
        $this->screencapOffset = null;
        $this->webRootDir;
    }
    
    /**
     * Get entityManager
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager(){
        return $this->entityManager;
    }
    
    /**
     * Get tvdbClient
     * @return \Moinax\TvDb\Client
     */
    public function getTvdbClient(){
        return $this->tvdbClient;
    }
    
    public function setScreencapRootDir($screencapRootDir){
        $this->screencapRootDir = preg_replace('/\/$/', '', trim($screencapRootDir));
        return $this;
    }
    
    public function setScreencapOffset($screencapOffset){
        $this->screencapOffset = (int)$screencapOffset;
        return $this;
    }
    
    public function setWebRootDir($webRootDir){
        $this->webRootDir = $webRootDir;
        return $this;
    }
    
    static function stripLeadingZeroes($text){
        if($text === null) return null;
        return preg_replace(self::REGEX_STRIP_LEADING_ZEROES, '', $text);
    }
    
    public function getUnprocessedMedia($batchSize = null){
        
        $queryBuilder = $this->entityManager->getRepository('KyoushuMediaBundle:Media')
                ->createQueryBuilder('m')
                ->setMaxResults($batchSize);
        
        $queryBuilder->where( $queryBuilder->expr()->isNull('m.processed') );
        $queryBuilder->orderBy('m.scanned', 'DESC');
        
        return $queryBuilder->getQuery()->getResult();
        
    }
    
    static function normalizeTvShowName($tvShowName){
        $tvShowName = preg_replace('/[\.]/', ' ', $tvShowName);
        $tvShowName = preg_replace('/\s+/', ' ', $tvShowName);
        return ucwords(trim($tvShowName));
    }
    
    static function normalizeMovieName($movieName){
        $movieName = preg_replace('/[\.]/', ' ', $movieName);
        $movieName = preg_replace('/\s+/', ' ', $movieName);
        return ucwords(trim($movieName));
    }
    
    static function extractFilenameMovieInfo($filename){
        
        $match = null;
        if(!preg_match(self::REGEX_MOVIE_INFO, $filename, $match)) return false;
        
        return array(
            'movieName' => self::normalizeMovieName($match['movieName']),
            'year' => $match['year']
        );
        
    }
    
    static function extractFilenameEpisodeInfo($filename){
        
        $match = null;
        if(!preg_match(self::REGEX_EPISODE_INFO, $filename, $match)) return false;
        
        if(isset($match['seasonNumberA']) && $match['seasonNumberA']) $seasonNumber = $match['seasonNumberA'];
        elseif(isset($match['seasonNumberB']) && $match['seasonNumberB']) $seasonNumber = $match['seasonNumberB'];
        else $seasonNumber = null;
        
        if(isset($match['episodeNumberA']) && $match['episodeNumberA']) $episodeNumber = $match['episodeNumberA'];
        elseif(isset($match['episodeNumberB']) && $match['episodeNumberB']) $episodeNumber = $match['episodeNumberB'];
        else $episodeNumber = null;
        
        return array(
            'tvShowName' => self::normalizeTvShowName($match['tvShowName']),
            'seasonNumber' => self::stripLeadingZeroes($seasonNumber),
            'episodeNumber' => self::stripLeadingZeroes($episodeNumber),
            'airDate' => (isset($match['airDate']) && $match['airDate'] ? new \DateTime($match['airDate']) : null)
        );
        
    }
    
    /**
     * Gets a TV show by name, creating one if necessary
     * @param type $tvShowName
     * @return type
     */
    public function getTvShow($tvShowName){
        
        $em = $this->getEntityManager();
        
        $rootTvShow = $em->getRepository('KyoushuMediaBundle:TvShow')->findOneBy(array('name' => $tvShowName));
        if($rootTvShow) return $rootTvShow;
        
        $tvShowAlias = $em->getRepository('KyoushuMediaBundle:TvShowAlias')->findOneBy(array('name' => $tvShowName));
        if($tvShowAlias) return $tvShowAlias->getTvShow();
        
        $tvdbClient = $this->getTvdbClient();
        $series = $tvdbClient->getSeries($show->getName());
        
        $serie = reset($series);
        
        if($serie){
            $existingTvShow = $em->getRepository('KyoushuMediaBundle:TvShow')->findOneBy(array('tvDbId' => $serie->id));
            if($existingTvShow){
                
                $alias = new TvShowAlias();
                $alias->setName($tvShowName);
                $existingTvShow->addAlias($alias);
                
                $em->persist($existingTvShow);
                $em->flush();
                
                return $existingTvShow;
            }
        }
            
        $newTvShow = new TvShow();
        $newTvShow->setName($tvShowName);
        
        $alias = new TvShowAlias();
        $alias->setName($tvShowName);
        $newTvShow->addAlias($alias);
        
        $em->persist($newTvShow);
        $em->flush();
        
        $this->processTvShow($newTvShow);
        
        return $newTvShow;
        
    }
    
    
    
    private function insertMediaFFmpegMetaData(Media $media){
        
        try{
            $probe = FFProbe::create();

            $streams = $probe->streams( $media->getAbsPath() );
            
            $videoStream = $streams->videos()->first();
            $audioStream = $streams->audios()->first();
            
            if($videoStream->has('codec_name')){
                $media->setVideoCodec( $videoStream->get('codec_name') );
            }
            
            if($audioStream && $audioStream->has('codec_name')){
                $media->setAudioCodec( $audioStream->get('codec_name') );
            }

            if($videoStream->has('duration')){
                $duration = $videoStream->get('duration');
                $media->setDuration($duration);
            }

            $dimensions = $videoStream->getDimensions();

            $media->setWidth( $dimensions->getWidth() );
            $media->setHeight( $dimensions->getHeight() );
           
        }
        catch(\Exception $e){
            // Do nothing, there can be so many unexpected problems with FFMPEG
            // We don't want them to interfere with the other processing methods
        }
        
        return $media;
        
    }
    
    private function insertMediaMovieInfo(Media $media){
        
        if($media->getCategory() === Media::CATEGORY_TV) return $media;
        
        $movieInfo = self::extractFilenameMovieInfo($media->getFilename());
        
        if($movieInfo === false) return $media;
        
        $media->setName($movieInfo['movieName']);
        $media->setReleaseDate(new \Datetime(sprintf('%s-01-01 00:00:00', $movieInfo['year'])));
        $media->setCategory(Media::CATEGORY_MOVIE);
        
        return $media;
        
    }
    
    private function insertMediaEpisodeInfo(Media $media){
        
        if($media->getCategory() === Media::CATEGORY_MOVIE) return $media;
        
        $episodeInfo = self::extractFilenameEpisodeInfo($media->getFilename());
        
        if($episodeInfo === false) return $media;
            
        $tvShow = $this->getTvShow($episodeInfo['tvShowName']);

        $media->setCategory(Media::CATEGORY_TV);
        $media->setTvShow($tvShow);
        
        if($episodeInfo['seasonNumber'] !== null){
            $media->setSeasonNumber($episodeInfo['seasonNumber']);
        }
        
        if($episodeInfo['episodeNumber'] !== null){
            $media->setEpisodeNumber($episodeInfo['episodeNumber']);
        }
        
        $media->setReleaseDate($episodeInfo['airDate']);
        
        if($tvShow->getTvDbId() && $media->getSeasonNumber() && $media->getEpisodeNumber()){
            
            try{
            
                $episode = $this->getTvdbClient()->getEpisode($tvShow->getTvDbId(), $media->getSeasonNumber(), $media->getEpisodeNumber());
                
                if($episode){
                    $media->setName( $episode->name );
                    $media->setReleaseDate( $episode->firstAired );
                    $media->setDescription( $episode->overview );
                }
                
            }
            catch(CurlException $e){ }
        }
            
        return $media;
        
    }
    
    private function insertMediaScreencap(Media $media){
        
        // Don't bother generating screencaps for private media
        if($media->getSource()->getPrivate()) return $media;
        
        $offset = $this->screencapOffset;
        $rootDir = $this->screencapRootDir;
        
        $groupNumber = floor($media->getId() / self::GROUP_DIR_DIVISION) * self::GROUP_DIR_DIVISION;
        $groupDir = sprintf('%s-%s', $groupNumber, $groupNumber + self::GROUP_DIR_DIVISION);
        
        $relPath = sprintf('%s/%s.jpg', $groupDir, $media->getId());
        $absPath = sprintf('%s/%s', $rootDir, $relPath);
                
        $dir = dirname($absPath);
        if(!file_exists($dir)) mkdir($dir, 0777, true);
        
        if(!file_exists($absPath)){
            
            try{
        
                $ffmpeg = FFMpeg::create();
                $video = $ffmpeg->open( $media->getAbsPath() );

                $seconds = $offset;
                $minutes = floor($seconds / 60);
                $seconds -= $minutes * 60;
                $hours = floor($minutes / 60);
                $minutes -= $hours * 60;

                $timecode = new TimeCode($hours, $minutes, $seconds, 0);
                $frame = $video->frame($timecode);
                $frame->save($absPath, false);
                
            }
            catch(\Alchemy\BinaryDriver\Exception\ExecutionFailureException $e){
                // Do nothing
            }
            catch(\FFMpeg\Exception\RuntimeException $e){
                // Do nothing
            }
            
        }
        
        if(file_exists($absPath)){
            $regexRealWebRootDir = sprintf('/^%s\//', preg_quote(realpath($this->webRootDir), '/'));
            $webPath = preg_replace($regexRealWebRootDir, '', realpath($absPath));
            $media->setScreencapWebPath($webPath);
        }
        
        return $media;
        
    }
    
    public function processTvShow(TvShow $show){
        
        $tvdbClient = $this->getTvdbClient();
        
        if($show->getTvDbId()){
            $serie = $tvdbClient->getSerie($show->getTvDbId());
        }
        else{
            $series = $tvdbClient->getSeries($show->getName());
            $serie = reset($series);
        }
        
        $imagesBaseDir = sprintf('%s/tvdb', $this->screencapRootDir);
        $regexRealWebRootDir = sprintf('/^%s\//', preg_quote(realpath($this->webRootDir), '/'));
        
        if($serie){
            
            $show->setName($serie->name);
            $show->setDescription($serie->overview);
            $show->setTvDbId($serie->id);
            
            $banners = $tvdbClient->getBanners( $serie->id );
            
            foreach($banners as $banner){
                
                if(!in_array($banner->type, array('fanart', 'poster', 'graphical'))) continue;
                
                if($banner->type === 'fanart' && $show->getFanArtWebPath()) continue;
                if($banner->type === 'poster' && $show->getPosterWebPath()) continue;
                if($banner->type === 'graphical' && $show->getBannerWebPath()) continue;
                
                $imageExt = preg_replace('/^.+\.([^\.]+)$/', '${1}', $banner->path);
                $imageUrl = sprintf('%s/banners/%s', $this->tvdbBaseUrl, $banner->path);
                $imageFilename = sprintf('%s.%s', $show->getId(), $imageExt);
                $imageAbsPath = sprintf('%s/%s/%s', $imagesBaseDir, $banner->type, $imageFilename);
                
                echo $imageUrl . "\n";
                
                if(!file_exists(dirname($imageAbsPath))){
                    mkdir(dirname($imageAbsPath), 0777, true);
                }
                
                if(!file_exists($imageAbsPath)){
                    file_put_contents($imageAbsPath, fopen($imageUrl, 'r'));
                }
                
                $imageWebPath = preg_replace($regexRealWebRootDir, '', realpath($imageAbsPath));
                
                if($banner->type === 'fanart') $show->setFanArtWebPath($imageWebPath);
                if($banner->type === 'poster') $show->setPosterWebPath($imageWebPath);
                if($banner->type === 'graphical') $show->setBannerWebPath($imageWebPath);
                
            }
            
        }
        
        $show->setProcessed(new \DateTime('now'));
            
        $em = $this->getEntityManager();
        $em->persist($show);
        $em->flush();
        
        return $show;
        
    }
    
    public function processMedia(Media $media){
        
        if(!$media->getId()) throw new \RuntimeException('Media entity must be persisted before processing');
        
        $this->insertMediaFFmpegMetaData($media);
        $this->insertMediaEpisodeInfo($media);
        $this->insertMediaMovieInfo($media);
        $this->insertMediaScreencap($media);
        
        if($media->getCategory() === null){
            $media->setCategory(Media::CATEGORY_UNKNOWN);
        }
        
        $media->setProcessed(new \DateTime('now'));
        
        $this->entityManager->persist($media);
        $this->entityManager->flush();
        
    }
    
}