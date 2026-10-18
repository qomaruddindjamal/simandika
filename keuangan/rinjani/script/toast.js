/**
 * Shows an Android-like Toast notification.
 * @param {string} message The text message to display.
 * @param {number} duration The duration in milliseconds the toast should be visible (default: 3000ms).
 * @param {string} type Optional. A class name for styling (e.g., 'success', 'error', 'info').
 * @param {string} position Optional. 'top' or 'bottom' (default: 'bottom').
 */
function showToast(message, duration, type, position)
{
    const $toastContainer = $('#toast-container');

    // --- Manage Toast Container Position ---
    // Remove existing position classes first
    $toastContainer.removeClass('toast-top toast-bottom');

    // Add the correct class based on the 'position' parameter
    if (position === 'top') {
        $toastContainer.addClass('toast-top');
    } else { // Default to bottom if 'position' is not 'top' or is undefined
        $toastContainer.addClass('toast-bottom');
    }
    // --- End Manage Toast Container Position ---

    // Create the toast element
    const $toast = $('<div class="toast-message"></div>');
    $toast.text(message);

    // Add type class if provided
    if (type) {
        $toast.addClass(type);
    }

    // Append to container
    $toastContainer.append($toast);

    // Animate fade in (using CSS transition by setting opacity)
    // Using a small timeout to ensure the browser registers the initial opacity:0
    // before transitioning to opacity:1.
    setTimeout(function() {
        $toast.css('opacity', 1);
    }, 10); // Small delay for transition to kick in

    // Set a timeout to fade out and remove the toast
    setTimeout(function() {
        $toast.css('opacity', 0); // Animate fade out

        // Remove toast from DOM after transition completes
        $toast.one('transitionend', function() {
            $(this).remove();
        });
    }, duration);
}