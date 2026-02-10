"use strict";
console.log('final.js loaded!');

//variable for navbar menu toggle
const header=document.querySelector('header');
const nav=document.querySelector('nav');
const navbarMenuBtn=document.querySelector('.navbar-menu-btn');

//variables for navbar search toggle
const navbarForm=document.querySelector('.navbar-form');
const navbarFormCloseBtn=document.querySelector('.navbar-form-close');
const navbarSearchBtn=document.querySelector('.navbar-search-btn');
const navbarFormSearch=document.querySelector('.navbar-form-search');

//navbar search toggle function
const searchBarIsActive = () => {
    if (!navbarForm) return;
    navbarForm.classList.toggle('active');
    
    // Check if navigation menu is also active and adjust search position
    if (navbarForm.classList.contains('active')) {
        if (nav && nav.classList.contains('active')) {
            navbarForm.classList.add('nav-open');
        }
    } else {
        navbarForm.classList.remove('nav-open');
    }
};

//navbar menu toggle function
function navIsActive(){
    if (header) header.classList.toggle('active');
    if (nav) nav.classList.toggle('active');
    if (navbarMenuBtn) navbarMenuBtn.classList.toggle('active');
    
    // Adjust search form position if it's active
    if (navbarForm && navbarForm.classList.contains('active')) {
        if (nav && nav.classList.contains('active')) {
            navbarForm.classList.add('nav-open');
        } else {
            navbarForm.classList.remove('nav-open');
        }
    }
}

if (navbarMenuBtn) navbarMenuBtn.addEventListener('click',navIsActive);
    
if (navbarSearchBtn) navbarSearchBtn.addEventListener('click',searchBarIsActive);
if (navbarFormCloseBtn) navbarFormCloseBtn.addEventListener('click',searchBarIsActive);


function showToast(message, type = 'success') {
    
    const existingToast = document.querySelector('.toast-notification');
    if (existingToast) existingToast.remove();

    
    const toast = document.createElement('div');
    toast.className = 'toast-notification';
    
    
    if (type === 'remove') {
        toast.classList.add('remove');
        toast.innerHTML = `<i class="ri-delete-bin-line"></i> ${message}`;
    } else {
        toast.innerHTML = `<i class="ri-checkbox-circle-line"></i> ${message}`;
    }

   
    document.body.appendChild(toast);

    
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);

   
    setTimeout(() => {
        toast.classList.remove('show');
      
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3000);
}

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', () => {

   
// SEARCH FUNCTIONALITY WITH LIVE SUGGESTIONS


const searchInput = document.querySelector('.navbar-form-search');
const searchForm = document.querySelector('.navbar-form');

if (searchInput && searchForm) {
    let searchTimeout;
    let suggestionsBox;

    // Create suggestions dropdown
    function createSuggestionsBox() {
        if (!suggestionsBox) {
            suggestionsBox = document.createElement('div');
            suggestionsBox.className = 'search-suggestions';
            searchForm.appendChild(suggestionsBox);
        }
        return suggestionsBox;
    }

    // Show suggestions
    function showSuggestions(suggestions) {
        const box = createSuggestionsBox();
        
        if (suggestions.length === 0) {
            box.innerHTML = '<div class="suggestion-item no-results">No movies found</div>';
            box.classList.add('active');
            return;
        }

        let html = '';
        suggestions.forEach(movie => {
            html += `
                <a href="movie_info.php?id=${movie.id}" class="suggestion-item">
                    <img src="${movie.poster}" alt="${movie.title}" class="suggestion-poster">
                    <div class="suggestion-info">
                        <div class="suggestion-title">${movie.title}</div>
                        <div class="suggestion-year">${movie.year || 'N/A'}</div>
                    </div>
                </a>
            `;
        });
        
        box.innerHTML = html;
        box.classList.add('active');
    }

    // Hide suggestions
    function hideSuggestions() {
        if (suggestionsBox) {
            suggestionsBox.classList.remove('active');
        }
    }

    // Fetch suggestions from server
    function fetchSuggestions(query) {
        if (query.length < 2) {
            hideSuggestions();
            return;
        }

        fetch(`search_suggestions.php?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                showSuggestions(data);
            })
            .catch(error => {
                console.error('Search error:', error);
                hideSuggestions();
            });
    }

    // Input event with debounce
    searchInput.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        const query = e.target.value.trim();
        
        searchTimeout = setTimeout(() => {
            fetchSuggestions(query);
        }, 300); // Wait 300ms after user stops typing
    });

    // Focus event
    searchInput.addEventListener('focus', (e) => {
        const query = e.target.value.trim();
        if (query.length >= 2) {
            fetchSuggestions(query);
        }
    });

    // Close suggestions when clicking outside
    document.addEventListener('click', (e) => {
        if (!searchForm.contains(e.target)) {
            hideSuggestions();
        }
    });

    // Prevent form submission if empty
    searchForm.addEventListener('submit', (e) => {
        const query = searchInput.value.trim();
        if (query.length < 2) {
            e.preventDefault();
            alert('Please enter at least 2 characters to search');
        }
    });
}
    
// DROPDOWN MENU FUNCTIONALITY
const dropdownBtn = document.querySelector('.dropdown-btn');
const dropdownContent = document.querySelector('.dropdown-content');
const dropdown = document.querySelector('.dropdown');

console.log('Dropdown elements:', { dropdownBtn, dropdownContent, dropdown });

// Toggle Category Menu on Click
if (dropdownBtn && dropdownContent) {
    dropdownBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('Dropdown button clicked');
        
        // Close user box if open
        const userBox = document.querySelector('.user-box');
        if (userBox) userBox.classList.remove('active');
        
        // Toggle dropdown
        dropdownContent.classList.toggle('show');
        dropdownBtn.classList.toggle('active');
        console.log('Dropdown show class:', dropdownContent.classList.contains('show'));
    });
}

// Close dropdown when clicking outside
document.addEventListener('click', (e) => {
    if (dropdownContent && dropdownBtn) {
        if (!dropdownContent.contains(e.target) && !dropdownBtn.contains(e.target)) {
            dropdownContent.classList.remove('show');
            if (dropdownBtn) dropdownBtn.classList.remove('active');
        }
    }
});

// IMPORTANT: Prevent dropdown from closing when clicking links inside
if (dropdownContent) {
    dropdownContent.addEventListener('click', (e) => {
        // Allow links to work normally
        if (e.target.tagName !== 'A') {
            e.stopPropagation();
        }
    });
}
    
    // Toggle user dropdown menu
    const userBtn = document.querySelector('.navbar-user-btn');
    const userBox = document.querySelector('.user-box');

    if (userBtn && userBox) {
        userBtn.addEventListener('click', (e) => {
            e.stopPropagation(); // Stop click from bubbling to document
            userBox.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!userBox.contains(e.target) && !userBtn.contains(e.target)) {
                userBox.classList.remove('active');
            }
        });

        // Prevent closing when clicking inside the user-box
        userBox.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }

    // Carousel and thumbnail functionality
    const carouselItems = document.querySelectorAll('.carousel .list > .item');
    const thumbnailItems = document.querySelectorAll('.thumbnails .item');
    const prevBtn = document.getElementById('prev');
    const nextBtn = document.getElementById('next');
    let currentIndex = 0;

    function showSlide(index, thumbnailElement = null) {
        // Remove active class from all carousel items
        carouselItems.forEach((item, i) => {
            item.classList.remove('active');
            item.style.zIndex = 0;
        });

        // Calculate position offset if clicked from thumbnail
        if (thumbnailElement) {
            const carousel = document.querySelector('.carousel .list');
            const thumbRect = thumbnailElement.getBoundingClientRect();
            const carouselRect = carousel.getBoundingClientRect();
            
            // Calculate relative position from center
            const offsetX = (thumbRect.left + thumbRect.width / 2) - (carouselRect.left + carouselRect.width / 2);
            const offsetY = (thumbRect.top + thumbRect.height / 2) - (carouselRect.top + carouselRect.height / 2);
            
            // Set CSS custom properties for animation
            const activeItem = carouselItems[index];
            activeItem.style.setProperty('--thumb-x', `${offsetX}px`);
            activeItem.style.setProperty('--thumb-y', `${offsetY}px`);
        }

        // Force reflow to restart animation
        void carouselItems[index].offsetWidth;

        // Add active class to current item
        carouselItems[index].classList.add('active');
        carouselItems[index].style.zIndex = 1;

        // Update thumbnail active state
        thumbnailItems.forEach((thumb, i) => {
            thumb.classList.toggle('active', i === index);
        });

        currentIndex = index;
    }

    // Thumbnail click event
    thumbnailItems.forEach((thumbnail, index) => {
        thumbnail.addEventListener('click', (e) => {
            showSlide(index, thumbnail);
        });
    });

    // Arrow button events
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            const newIndex = (currentIndex - 1 + carouselItems.length) % carouselItems.length;
            showSlide(newIndex);
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            const newIndex = (currentIndex + 1) % carouselItems.length;
            showSlide(newIndex);
        });
    }

    // Initialize first slide and start auto-cycle
    if (carouselItems.length > 0) {
        showSlide(0);
        
        // Auto-cycle through slides every 9 seconds
        setInterval(() => {
            const newIndex = (currentIndex + 1) % carouselItems.length;
            showSlide(newIndex);
        }, 9000);
    }

    // Filter functionality
    console.log('DOMContentLoaded fired - filter setup starting');
    
    const genreSelect = document.getElementById('genre');
    const yearSelect = document.getElementById('year');
    const loadMoreBtn = document.querySelector('.load-more-btn');
    const movieGrid = document.querySelector('.movie-grid');

    console.log('genreSelect found:', genreSelect);
    console.log('yearSelect found:', yearSelect);
    console.log('loadMoreBtn found:', loadMoreBtn);
    console.log('movieGrid found:', movieGrid);

    // Check if essential elements exist (only need movieGrid and loadMoreBtn for radio-only mode)
    if (!loadMoreBtn || !movieGrid) {
        console.log('Essential filter elements not found on this page');
        return;
    }

    let currentOffset = 12; 
    let isFetching = false; // Prevent double fetch
    let loadedMovieIds = []; // Track already loaded movie IDs to prevent duplicates

    // Initialize loadedMovieIds with movies already on the page
    function initLoadedMovies() {
        const existingCards = movieGrid.querySelectorAll('.movie-card .bookmark');
        existingCards.forEach(bookmark => {
            const movieId = bookmark.getAttribute('data-movie-id');
            if (movieId && !loadedMovieIds.includes(movieId)) {
                loadedMovieIds.push(movieId);
            }
        });
        console.log('Initially loaded movie IDs:', loadedMovieIds);
    }
    initLoadedMovies();

    function fetchMovies(isLoadMore = false) {
        // Prevent multiple simultaneous requests
        if (isFetching) {
            console.log('Already fetching, ignoring request');
            return;
        }
        isFetching = true;
        
        console.log('fetchMovies called with isLoadMore:', isLoadMore);
        
        // Use select values if they exist, otherwise use 'all'
        let selectedGenre = genreSelect ? genreSelect.value : 'all';
        let selectedYear = yearSelect ? yearSelect.value : 'all';
        
        console.log('Selected genre:', selectedGenre);
        console.log('Selected year:', selectedYear);
        
        let offsetToSend = isLoadMore ? currentOffset : 0;
        console.log('Offset to send:', offsetToSend);

        // Use URLSearchParams instead of FormData for better compatibility
        const params = new URLSearchParams();
        params.append('genre', selectedGenre);
        params.append('year', selectedYear);
        params.append('offset', offsetToSend);
        
        // Get selected sort option (popular or newest)
        const popularRadio = document.getElementById('popular');
        const sortValue = (popularRadio && popularRadio.checked) ? 'popular' : 'newest';
        params.append('sort', sortValue);
        
        // Send already loaded movie IDs to exclude duplicates
        if (isLoadMore && loadedMovieIds.length > 0) {
            params.append('exclude_ids', loadedMovieIds.join(','));
        }

        console.log('Sending request to load_more.php with params:', params.toString());

        fetch('load_more.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: params.toString()
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response ok:', response.ok);
            return response.text();
        })
        .then(data => {
            console.log('Raw data received:', data);
            
            // Check if there's a PHP error in the response
            if (data.includes('Fatal error') || data.includes('Warning') || data.includes('Notice')) {
                console.error('PHP Error detected:', data);
                isFetching = false;
                return;
            }
            
            if (isLoadMore) {
                // Load More - append to existing
                if (data.trim() !== "" && !data.includes("No movies found")) {
                    movieGrid.insertAdjacentHTML('beforeend', data);
                    currentOffset += 6;
                    // Update loadedMovieIds with newly loaded movies
                    const newCards = movieGrid.querySelectorAll('.movie-card .bookmark');
                    newCards.forEach(bookmark => {
                        const movieId = bookmark.getAttribute('data-movie-id');
                        if (movieId && !loadedMovieIds.includes(movieId)) {
                            loadedMovieIds.push(movieId);
                        }
                    });
                    console.log('Updated loaded movie IDs:', loadedMovieIds);
                } else {
                    loadMoreBtn.innerText = "No More Movies";
                    loadMoreBtn.disabled = true;
                }
            } else {
                // Filter changed - replace content (shows 12 movies)
                movieGrid.innerHTML = data;
                currentOffset = 12; 
                loadMoreBtn.innerText = "Load More"; 
                loadMoreBtn.disabled = false;
                // Reset and reinitialize loaded movie IDs
                loadedMovieIds = [];
                initLoadedMovies();
            }
            isFetching = false;
        })
        .catch(error => {
            console.error('Fetch error:', error);
            isFetching = false;
        });
    }

    // Only add event listeners if selects exist
    if (genreSelect) {
        genreSelect.addEventListener('change', function() {
            console.log('Genre changed to:', this.value);
            fetchMovies(false);
        });
    }
    
    if (yearSelect) {
        yearSelect.addEventListener('change', function() {
            console.log('Year changed to:', this.value);
            fetchMovies(false);
        });
    }

    // Radio button filter (Popular / Newest)
    const popularRadio = document.getElementById('popular');
    const newestRadio = document.getElementById('newest');
    
    if (popularRadio) {
        popularRadio.addEventListener('change', function() {
            if (this.checked) {
                console.log('Popular filter selected');
                fetchMovies(false);
            }
        });
    }
    
    if (newestRadio) {
        newestRadio.addEventListener('change', function() {
            if (this.checked) {
                console.log('Newest filter selected');
                fetchMovies(false);
            }
        });
    }

    loadMoreBtn.addEventListener('click', () => fetchMovies(true));

});

// Bookmark functionality - Add to Collection
document.addEventListener('click', function(e) {
    const bookmark = e.target.closest('.bookmark');
    if (!bookmark) return;
    
    e.preventDefault();
    e.stopPropagation();
    
    const movieId = bookmark.getAttribute('data-movie-id');
    if (!movieId) {
        console.log('No movie ID found on bookmark');
        return;
    }
    
    console.log('Bookmark clicked for movie:', movieId);
    
    const icon = bookmark.querySelector('i');
    const movieCard = bookmark.closest('.movie-card');
    
    fetch('add_collection.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'movie_id=' + movieId
    })
    .then(response => {
        // console.log('Response status:', response.status); 
        return response.text();
    })
    .then(text => {
        try {
            return JSON.parse(text);
        } catch (e) {
            console.error('JSON parse error:', e);
            console.log('Raw text:', text); // Debug
            return null;
        }
    })
    .then(data => {
        if (!data) return;
        
        console.log('Parsed data:', data);
        
        if (data.success) {
            if (data.action === 'added') {
                // --- add to collection ---
                if (icon) {
                    icon.classList.remove('ri-bookmark-line');
                    icon.classList.add('ri-bookmark-fill');
                }
                bookmark.classList.add('bookmarked');
                
                // Update button text and style for movie_info.php
                const btnText = bookmark.querySelector('.btn-text');
                if (btnText) {
                    btnText.textContent = 'Remove from Collection';
                    bookmark.style.background = 'var(--live-indicator)';
                }
                
               showToast('Added to collection!');
            } else {
                // --- remove form collection ---
                if (icon) {
                    icon.classList.remove('ri-bookmark-fill');
                    icon.classList.add('ri-bookmark-line');
                }
                bookmark.classList.remove('bookmarked');
                
                // Update button text and style for movie_info.php
                const btnText = bookmark.querySelector('.btn-text');
                if (btnText) {
                    btnText.textContent = 'Add to Collection';
                    bookmark.style.background = '';
                }
                
                
                if (window.location.href.includes('collections.php') && movieCard) {
                    
                
                    movieCard.style.transition = 'all 0.5s ease';
                    movieCard.style.opacity = '0';
                    movieCard.style.transform = 'scale(0.8)';
                    
                  
                    setTimeout(() => {
                        movieCard.remove();
                        
                      
                        const grid = document.querySelector('.movie-grid');
                        const remainingCards = document.querySelectorAll('.movie-card');
                        
                        if (remainingCards.length === 0 && grid) {
                            grid.innerHTML = `
                                <div class="empty-state">
                                    <i class="ri-bookmark-line empty-icon"></i>
                                    <p class="empty-title">Your collection is empty</p>
                                    <p class="empty-desc">Click the bookmark icon on movies to add them here!</p>
                                    <a href="index.php" class="browse-btn">Browse Movies</a>
                                </div>
                            `;
                        }
                    }, 500);
                }
                showToast('Removed from collection', 'remove');
            }
        } else {
           
            alert(data.message);
            if(data.message.includes('login')) {
                window.location.href = 'login.php';
            }
        }
    })
    .catch(error => {
        console.error('Bookmark error:', error);
    });
});

// Year filter functionality for category page
document.addEventListener('DOMContentLoaded', function() {
    const yearSelect = document.getElementById('year');
    const movieCards = document.querySelectorAll('.movie-card');
    
    if (yearSelect && movieCards.length > 0) {
        yearSelect.addEventListener('change', function() {
            const selectedValue = this.value;
            let visibleCount = 0;
            
            movieCards.forEach(card => {
                const cardYear = parseInt(card.querySelector('.card-year').textContent);
                let showCard = false;
                
                if (selectedValue === 'all') {
                    showCard = true;
                } else if (selectedValue.includes('-')) {
                    // Year range (e.g., "2024-2025")
                    const [startYear, endYear] = selectedValue.split('-').map(Number);
                    showCard = cardYear >= startYear && cardYear <= endYear;
                } else {
                    // Single year (e.g., "2026")
                    showCard = cardYear === parseInt(selectedValue);
                }
                
                if (showCard) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Show empty state if no movies match
            const movieGrid = document.querySelector('.movie-grid');
            let emptyState = movieGrid.querySelector('.empty-state');
            
            if (visibleCount === 0) {
                if (!emptyState) {
                    emptyState = document.createElement('div');
                    emptyState.className = 'empty-state';
                    emptyState.innerHTML = `
                        <i class="ri-movie-2-line empty-icon"></i>
                        <p class="empty-title">No movies found for this year</p>
                    `;
                    movieGrid.appendChild(emptyState);
                }
                emptyState.style.display = '';
            } else if (emptyState) {
                emptyState.style.display = 'none';
            }
        });
    }
});

// NEW REVIEW SUBMISSION (for movie_info.php)
document.addEventListener('DOMContentLoaded', function() {
    const reviewForm = document.getElementById('review-form');
    
    if (!reviewForm) return;
    
    reviewForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const movieId = reviewForm.getAttribute('data-movie-id');
        const rating = document.getElementById('review-rating').value;
        const comment = document.getElementById('review-comment').value.trim();
        
        if (!comment) {
            showToast('Please enter a comment', 'remove');
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'submit_review');
        formData.append('movie_id', movieId);
        formData.append('rating', rating);
        formData.append('comment', comment);
        
        fetch('movie_info.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Review posted successfully!');
                
                // Update rating circle
                const ratingCircle = document.querySelector('.rating-circle');
                if (ratingCircle && data.new_average_rating) {
                    ratingCircle.textContent = data.new_average_rating;
                }
                
                // Add the new review to the list
                const reviewsList = document.querySelector('.reviews-list');
                const noReviewsMsg = reviewsList.querySelector('p[style*="color: #888"]');
                if (noReviewsMsg) {
                    noReviewsMsg.remove();
                }
                
                const newReviewHTML = `
                    <div class="review-item" data-review-id="${data.review_id}">
                        <div class="review-avatar">
                            ${data.avatar_url ? `<img src="${data.avatar_url}" alt="${data.username}">` : data.username.charAt(0).toUpperCase()}
                        </div>
                        <div class="review-content">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <h4>${data.username}</h4>
                                <div class="review-actions">
                                    <button data-action="edit">
                                        <i class="ri-edit-line"></i> Edit
                                    </button>
                                    <button data-action="delete">
                                        <i class="ri-delete-bin-line"></i> Delete
                                    </button>
                                </div>
                            </div>
                            <div class="stars">
                                <i class="ri-star-fill"></i> <span class="review-rating">${data.rating}</span> / 10
                            </div>
                            <div class="review-display">
                                <p class="review-text">${data.comment}</p>
                            </div>
                            <div class="review-edit-form" style="display: none;">
                                <div style="margin-bottom: 10px;">
                                    <label style="color: #aaa; font-size: 12px;">Rating:</label>
                                    <select class="edit-rating" style="background: var(--rich-black-fogra-29); color: white; padding: 8px; border-radius: 5px; border: 1px solid rgba(255,255,255,0.1); margin-left: 10px;">
                                        ${[1,2,3,4,5,6,7,8,9,10].map(i => `<option value="${i}" ${i == data.rating ? 'selected' : ''}>${i}</option>`).join('')}
                                    </select>
                                </div>
                                <textarea class="edit-comment" style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); color: white; padding: 10px; border-radius: 8px; min-height: 80px; resize: vertical; font-family: inherit;">${data.comment}</textarea>
                                <div style="margin-top: 10px; display: flex; gap: 10px;">
                                    <button type="button" class="save-edit-btn" style="padding: 8px 20px; background: var(--light-azure); color: white; border: none; border-radius: 5px; cursor: pointer;">Save</button>
                                    <button type="button" class="cancel-edit-btn" style="padding: 8px 20px; background: rgba(255,255,255,0.1); color: white; border: none; border-radius: 5px; cursor: pointer;">Cancel</button>
                                </div>
                            </div>
                            <div class="review-date">${data.date}</div>
                        </div>
                    </div>
                `;
                
                reviewsList.insertAdjacentHTML('afterbegin', newReviewHTML);
                
                // Clear the form
                document.getElementById('review-comment').value = '';
                document.getElementById('review-rating').value = '10';
                
                // Attach event listeners to new buttons
                const newReview = reviewsList.querySelector('.review-item[data-review-id="' + data.review_id + '"]');
                const editBtn = newReview.querySelector('[data-action="edit"]');
                const deleteBtn = newReview.querySelector('[data-action="delete"]');
                
                editBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    // Trigger the edit functionality (will be handled by existing code on page reload)
                    location.reload();
                });
                
                deleteBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    location.reload();
                });
                
            } else {
                showToast(data.message || 'Error posting review', 'remove');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error posting review', 'remove');
        });
    });
});

// REVIEW EDIT & DELETE FUNCTIONALITY (for movie_info.php)
document.addEventListener('DOMContentLoaded', function() {
    // Only run if we're on movie_info.php page
    if (!document.querySelector('.reviews-section')) {
        return; // Exit if not on movie info page
    }

    console.log('Review edit/delete script loaded');

    const editButtons = document.querySelectorAll('[data-action="edit"]');
    const deleteButtons = document.querySelectorAll('[data-action="delete"]');

    console.log('Edit buttons found:', editButtons.length);
    console.log('Delete buttons found:', deleteButtons.length);

    editButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Edit button clicked!');
            editReview(this);
        });
    });

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Delete button clicked!');
            deleteReview(this);
        });
    });

    function editReview(editButton) {
        console.log('editReview function called');
        
        const reviewItem = editButton.closest('.review-item');
        const reviewId = reviewItem.getAttribute('data-review-id');
        
        const reviewDisplay = reviewItem.querySelector('.review-display');
        const reviewEditForm = reviewItem.querySelector('.review-edit-form');
        const reviewActions = reviewItem.querySelector('.review-actions');
        
        // Show edit form, hide display
        reviewDisplay.style.display = 'none';
        reviewEditForm.style.display = 'block';
        reviewActions.style.display = 'none';
        
        // Handle Save button
        const saveBtn = reviewItem.querySelector('.save-edit-btn');
        const cancelBtn = reviewItem.querySelector('.cancel-edit-btn');
        
        // Remove old event listeners by cloning
        const newSaveBtn = saveBtn.cloneNode(true);
        const newCancelBtn = cancelBtn.cloneNode(true);
        saveBtn.parentNode.replaceChild(newSaveBtn, saveBtn);
        cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
        
        newSaveBtn.addEventListener('click', function() {
            const newRating = reviewItem.querySelector('.edit-rating').value;
            const newComment = reviewItem.querySelector('.edit-comment').value.trim();
            
            if (newComment === '') {
                showToast('Please enter a comment', 'remove');
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'update_review');
            formData.append('review_id', reviewId);
            formData.append('rating', newRating);
            formData.append('comment', newComment);

            fetch('movie_info.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    // Update the display without reloading
                    reviewItem.querySelector('.review-text').textContent = newComment;
                    reviewItem.querySelector('.review-rating').textContent = newRating;
                    
                    // Update the rating circle if exists
                    const ratingCircle = document.querySelector('.rating-circle');
                    if (ratingCircle && data.new_average_rating) {
                        ratingCircle.textContent = data.new_average_rating;
                    }
                    
                    // Hide edit form, show display
                    reviewDisplay.style.display = 'block';
                    reviewEditForm.style.display = 'none';
                    reviewActions.style.display = 'flex';
                    
                    showToast("Review updated successfully!");
                } else {
                    showToast("Error: " + data.message, 'remove');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast("Error updating review", 'remove');
            });
        });
        
        newCancelBtn.addEventListener('click', function() {
            // Hide edit form, show display
            reviewDisplay.style.display = 'block';
            reviewEditForm.style.display = 'none';
            reviewActions.style.display = 'flex';
        });
    }

    function deleteReview(deleteButton) {
        console.log('deleteReview function called');
        
        const reviewItem = deleteButton.closest('.review-item');
        const reviewId = reviewItem.getAttribute('data-review-id');
        const urlParams = new URLSearchParams(window.location.search);
        const movieId = urlParams.get('id');

        if (confirm("Are you sure you want to delete this review?")) {
            const formData = new FormData();
            formData.append('action', 'delete_review');
            formData.append('review_id', reviewId);
            formData.append('movie_id', movieId);

            fetch('movie_info.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    showToast("Review deleted!", "remove");
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Critical error deleting review.");
            });
        }
    }
});
// MY REVIEWS PAGE - Edit & Delete functionality
document.addEventListener('DOMContentLoaded', function() {
    // Only run if we're on my_reviews.php page (check for review cards)
    if (!document.querySelector('.my-review-card')) {
        return;
    }

    console.log('My Reviews page script loaded');

    const editButtons = document.querySelectorAll('.my-review-card [data-action="edit"]');
    const deleteButtons = document.querySelectorAll('.my-review-card [data-action="delete"]');

    editButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            editReviewFromMyReviews(this);
        });
    });

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            deleteReviewFromMyReviews(this);
        });
    });

    function editReviewFromMyReviews(editButton) {
        const reviewCard = editButton.closest('.my-review-card');
        const reviewId = reviewCard.getAttribute('data-review-id');
        
        const reviewDisplay = reviewCard.querySelector('.review-display');
        const reviewEditForm = reviewCard.querySelector('.review-edit-form');
        const reviewActions = reviewCard.querySelector('.review-actions');
        
        // Show edit form, hide display
        reviewDisplay.style.display = 'none';
        reviewEditForm.style.display = 'block';
        reviewActions.style.display = 'none';
        
        // Handle Save button
        const saveBtn = reviewCard.querySelector('.save-edit-btn');
        const cancelBtn = reviewCard.querySelector('.cancel-edit-btn');
        
        // Remove old event listeners by cloning
        const newSaveBtn = saveBtn.cloneNode(true);
        const newCancelBtn = cancelBtn.cloneNode(true);
        saveBtn.parentNode.replaceChild(newSaveBtn, saveBtn);
        cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
        
        newSaveBtn.addEventListener('click', function() {
            const newRating = reviewCard.querySelector('.edit-rating').value;
            const newComment = reviewCard.querySelector('.edit-comment').value.trim();
            
            if (newComment === '') {
                showToast('Please enter a comment', 'remove');
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'update_review');
            formData.append('review_id', reviewId);
            formData.append('rating', newRating);
            formData.append('comment', newComment);

            fetch('my_reviews.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    // Update the display without reloading
                    reviewCard.querySelector('.review-text').textContent = newComment;
                    reviewCard.querySelector('.review-rating').textContent = newRating;
                    
                    // Hide edit form, show display
                    reviewDisplay.style.display = 'block';
                    reviewEditForm.style.display = 'none';
                    reviewActions.style.display = 'flex';
                    
                    showToast("Review updated successfully!");
                } else {
                    showToast("Error: " + data.message, 'remove');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast("Error updating review", 'remove');
            });
        });
        
        newCancelBtn.addEventListener('click', function() {
            // Hide edit form, show display
            reviewDisplay.style.display = 'block';
            reviewEditForm.style.display = 'none';
            reviewActions.style.display = 'flex';
        });
    }

    function deleteReviewFromMyReviews(deleteButton) {
        const reviewCard = deleteButton.closest('.my-review-card');
        const reviewId = reviewCard.getAttribute('data-review-id');
        const movieId = reviewCard.getAttribute('data-movie-id');

        if (confirm("Are you sure you want to delete this review?")) {
            const formData = new FormData();
            formData.append('action', 'delete_review');
            formData.append('review_id', reviewId);
            formData.append('movie_id', movieId);

            fetch('my_reviews.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    showToast("Review deleted!", "remove");
                    
                    // Animate card removal
                    reviewCard.style.transition = 'all 0.5s ease';
                    reviewCard.style.opacity = '0';
                    reviewCard.style.transform = 'scale(0.8)';
                    
                    setTimeout(() => {
                        reviewCard.remove();
                        
                        // Check if no reviews left
                        const remainingCards = document.querySelectorAll('.my-review-card');
                        if (remainingCards.length === 0) {
                            location.reload();
                        }
                    }, 500);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Critical error deleting review.");
            });
        }
    }
});





