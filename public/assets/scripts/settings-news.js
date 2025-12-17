document.addEventListener('DOMContentLoaded', () => {
    const checkAllButton = document.querySelector('[data-trigger="check-all-news-categories"]');
    const uncheckAllButton = document.querySelector('[data-trigger="uncheck-all-news-categories"]');
    const checkboxes = document.querySelectorAll('input[name="set_newscategories[]"]');

    checkAllButton.addEventListener('click', () => {
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
    });

    uncheckAllButton.addEventListener('click', () => {
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
    });
});