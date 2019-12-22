(function() {
    
    const form = document.getElementById('filter_form');
    const pageLimitSelect = document.getElementById('page_limit_select');
    const deleteButton = document.getElementById('delete_button');
    const listCheckbox = document.getElementById('list_checkbox');
    const listIdCheckboxes = document.querySelectorAll('.list-id-checkbox');
    const listActions = document.querySelectorAll('.list-action');
    const messageDeleteButtons = document.querySelectorAll('.notification .delete');
    
    function getCountOfCheckedIds() {
        var result = 0;
        listIdCheckboxes.forEach(function (e) {
            if (e.checked) {
                result++;
            }
        });
        return result;
    }
    
    function getCheckedIds() {
        var result = [];
        listIdCheckboxes.forEach(function (e) {
            if (e.checked) {
                result.push(e.value);
            }
        });
        return result;
    }
    
    function changeDeleteButtonClassList() {
        if (!deleteButton) {
            return;
        }
        const selected = getCountOfCheckedIds();
        if (selected) {
            deleteButton.classList.add('is-warning');
        } else {
            deleteButton.classList.remove('is-warning');
        }        
    }

    if (pageLimitSelect) {
        pageLimitSelect.addEventListener('change', function() {
            var url = new URL(pageLimitSelect.getAttribute('data-url'));
            url.searchParams.set('page_limit', pageLimitSelect.value);
            window.location.href = url;
        });    
    }
    
    listIdCheckboxes.forEach(function (e) {
        e.addEventListener('change', function(event) {
            if (listCheckbox.checked) {
                listCheckbox.checked = event.target.checked;
            }
            changeDeleteButtonClassList();
        });
    });    

    if (listCheckbox) {
        listCheckbox.addEventListener('change', function() {
            listIdCheckboxes.forEach(function (e) {
                e.checked = listCheckbox.checked;            
            });
            changeDeleteButtonClassList();
        });
    }
    
    listActions.forEach(function (e) {
        const action = e.getAttribute('data-action');
        const confirmText = e.getAttribute('data-confirm');
        e.addEventListener('click', function() {
            const selected = getCountOfCheckedIds();
            if (!selected) {
                return;
            }
            if (confirmText && !confirm(confirmText)) {
                return;
            }
            var url = new URL(action);
            url.searchParams.set('ids', getCheckedIds());
            window.location.href = url;
        });
    });
    
    messageDeleteButtons.forEach(function (e) {
        e.addEventListener('click', function(event) {
            event.target.parentElement.style.display = 'none';
        });
    });
    
})();
