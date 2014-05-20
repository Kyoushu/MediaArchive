$(function(){
    
    function initWidgets(){
        
        // _________________________ [data-collection]
   
        $('[data-collection]:not([data-initialised])').each(function(){
                
            var collectionElement = $(this);
            collectionElement.attr('data-initialised', 1);

            var childContainerElement = collectionElement.find('[data-collection-children]:first');
            var childIndex = childContainerElement.find('[data-collection-children]').length;

            var addButton = collectionElement.find('[data-collection-add]');
            var prototype = collectionElement.attr('data-collection-prototype');

            childContainerElement.on('click', '[data-collection-remove]', function(){
                var removeButton = $(this);
                var child = removeButton.closest('[data-collection-child]');
                child.remove();
            });

            addButton.on('click', function(){

                childIndex++;
                var index = childIndex;

                var childHtml = prototype
                    .replace(/__name__label__/g, childIndex)
                    .replace(/__name__/g, childIndex);

                var child = $(childHtml);
                childContainerElement.append(child);

                initWidgets();

            });

        });

        // _________________________ [data-entity-table-wrapper]

        $('form[data-entity-table-wrapper]:not([data-initialised])').each(function(){
            
            var form = $(this);
            form.attr('data-initialised', 1);

            var lastCheckboxClicked;
            var shiftDown = false;

            $(document).on('keydown', function(e){
               if(e.keyCode === 16) shiftDown = true;
            });

            $(document).on('keyup', function(e){
               if(e.keyCode === 16) shiftDown = false;
            });

            form.find('[data-table-row-checkbox]').on('click', function(){

                var checkbox = $(this);
                var table = checkbox.closest('table');

                function checkTableRowCheckbox(index){
                    table.find('tr:eq(' + (index + 1) + ') [data-table-row-checkbox]').prop('checked', true);
                }

                if(shiftDown && lastCheckboxClicked){

                    var firstTableRowIndex = checkbox.closest('tr').index();
                    var secondTableRowIndex = lastCheckboxClicked.closest('tr').index();

                    if(firstTableRowIndex <= secondTableRowIndex){
                        for(var index = firstTableRowIndex; index <= secondTableRowIndex; index++){
                            checkTableRowCheckbox(index);
                        }
                    }
                    else{
                        for(var index = secondTableRowIndex; index <= firstTableRowIndex; index++){
                            checkTableRowCheckbox(index);
                        }
                    }

                }

                lastCheckboxClicked = checkbox;
            });

            form.find('[data-table-deselect-all-row-checkboxes]').on('click', function(){

                var button = $(this);
                var form = button.closest('form');

                form.find('[data-table-row-checkbox]').prop('checked', false);

            });

            form.find('[data-table-select-all-row-checkboxes]').on('click', function(){

                var button = $(this);
                var form = button.closest('form');

                form.find('[data-table-row-checkbox]').prop('checked', true);

            });

            form.find('[data-table-invert-row-checkboxes]').on('click', function(){

                var button = $(this);
                var form = button.closest('form');

                form.find('[data-table-row-checkbox]').each(function(){
                    var checkbox = $(this);
                    var checked = checkbox.is(':checked');
                    checkbox.prop('checked', !checked);
                });

            });

        });

        // _________________________ [data-entity-autocomplete]
        
        $('[data-entity-autocomplete]:not([data-initialised])').each(function(){
            
            var container = $(this);
            container.attr('data-initialised', 1);
            
            var config = JSON.parse( container.attr('data-entity-autocomplete') );

            var searchElement = container.find('[data-entity-autocomplete-search]');
            var valueElement = container.find('[data-entity-autocomplete-value]');
            var suggestionsElement = container.find('[data-entity-autocomplete-suggestions]');
            var idElement = container.find('[data-entity-autocomplete-id]');

            var searchDelay = 500;
            var searchTimeout;
            
            function hideSuggestions(){
                suggestionsElement.empty().css('display', 'none');
                searchElement.val('');
            }

            function showSuggestions(result){

                suggestionsElement.empty().css('display', 'block');

                var ulElement = $('<ul>').appendTo(suggestionsElement);

                $.each(result, function(index, option){
                    ulElement.append(
                        $('<li>').append(
                            $('<a>', {'href': '#'})
                                .text(option.label)
                                .click(function(e){

                                    e.preventDefault();
                                    hideSuggestions();

                                    idElement.text(option.value);
                                    valueElement.val(option.value);
                                    searchElement.val('').attr('placeholder', option.label)

                                })
                        )
                    );
                });
            }

            function search(){

                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function(){

                    var searchString = searchElement.val();

                    var url = Routing.generate('kyoushu_media_admin_entity_autocomplete', {
                        'entityClass': config.entityClass,
                        'property': config.property,
                        'searchProperties': config.searchProperties,
                        'searchString': searchString
                    });

                    $.ajax({
                        'url': url,
                        'datatype': 'json',
                        'success': function(response){
                            showSuggestions(response.result);
                        }
                    });

                }, searchDelay);

            }

            searchElement.on('keyup', search);
            searchElement.on('blur', function(){
                setTimeout(hideSuggestions, 250);
            });

        });
        
        // _________________________ [data-entity-table-context]
        
        $('[data-entity-table-context]:not([data-initialised])').each(function(){
            
            var button = $(this);
            button.attr('data-initialised', 1);
            
            var form = button.closest('form');
            
            var revealId = button.attr('data-reveal-id');
            var revealForm = $('#' + revealId + ' form');
            
            var entityIdsInput = revealForm.find('[data-entity-table-context-entity-ids]');
            
            button.on('click', function(){
                
                var entityIds = [];
                form.find('[data-table-row-checkbox]:checked').each(function(){
                    entityIds.push( $(this).val() );
                });

                console.log(entityIds);

                entityIdsInput.val(entityIds.join(','));
                
            });
            
            
            
        });
        
    }
    
    initWidgets();
    
});