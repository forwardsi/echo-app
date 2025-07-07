   // Wait until the DOM is fully loaded
    document.addEventListener('DOMContentLoaded', () => {
        // Handle the "Read More" button clicks
        const readMoreButtons = document.querySelectorAll('.read-more-btn');
        
        readMoreButtons.forEach(button => {
            button.addEventListener('click', () => {
                const reviewId = button.getAttribute('data-review-id');
                const reviewText = document.querySelector(`#review-${reviewId}`);

                // Toggle between expanded and collapsed states
                if (reviewText.classList.contains('expanded')) {
                    reviewText.classList.remove('expanded');
                    button.textContent = 'Read More';
                } else {
                    reviewText.classList.add('expanded');
                    button.textContent = 'Read Less';
                }
            });
        });
    });