console.log("Search script has loaded and is running!");

document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM is fully loaded. Script is looking for search elements now...");

    const searchIcon = document.getElementById('search-icon');
    const searchBar = document.getElementById('search-bar');

    // This will show us exactly what the script found
    console.log("Search Icon Element found:", searchIcon);
    console.log("Search Bar Element found:", searchBar);

    if (searchIcon && searchBar) {
        console.log("SUCCESS: Both elements were found! Adding the click function.");
        
        searchIcon.addEventListener('click', function(event) {
            event.stopPropagation();
            searchBar.classList.toggle('active');
            if (searchBar.classList.contains('active')) {
                searchBar.querySelector('input').focus();
            }
        });

        document.addEventListener('click', function(event) {
            if (!searchBar.contains(event.target) && !searchIcon.contains(event.target)) {
                searchBar.classList.remove('active');
            }
        });

    } else {
        console.error("FAILURE: One or both search elements were NOT found in the document.");
    }
});
// --- NOTIFICATION SYSTEM ---
const bellIcon = document.getElementById('notification-bell');
const notificationCount = document.getElementById('notification-count');
const dropdown = document.getElementById('notifications-dropdown');
const notificationList = document.getElementById('notification-list');
const markAllReadBtn = document.getElementById('mark-all-read');

// Function to fetch and display notifications
const fetchNotifications = async () => {
    try {
        const response = await fetch('../api/notifications.php');
        const data = await response.json();

        // Update badge count
        if (data.unread_count > 0) {
            notificationCount.textContent = data.unread_count;
            notificationCount.style.display = 'flex';
        } else {
            notificationCount.style.display = 'none';
        }

        // Populate dropdown
        notificationList.innerHTML = ''; // Clear previous items
        if (data.notifications.length > 0) {
            data.notifications.forEach(notif => {
                const item = document.createElement('a');
                item.href = notif.link + '?notif_id=' + notif.id;
                item.className = 'notification-item';
                item.dataset.id = notif.id;
                item.innerHTML = `
                    ${notif.message}
                    <span class="notification-item-time">${new Date(notif.created_at).toLocaleString()}</span>
                `;
                notificationList.appendChild(item);
            });
        } else {
            notificationList.innerHTML = '<div class="no-notifications">No new notifications</div>';
        }
    } catch (error) {
        console.error('Error fetching notifications:', error);
    }
};

// Function to mark a notification as read
const markAsRead = async (id) => {
    try {
        await fetch('../api/notifications.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'mark_read', id: id })
        });
        fetchNotifications(); // Refresh the list
    } catch (error) {
        console.error('Error marking notification as read:', error);
    }
};

if (bellIcon) {
    // Toggle dropdown
    bellIcon.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdown.classList.toggle('active');
    });

    // Mark single notification as read when its link is clicked
    notificationList.addEventListener('click', (e) => {
        const item = e.target.closest('.notification-item');
        if (item) {
            markAsRead(item.dataset.id);
        }
    });
    
    // Mark all as read
    markAllReadBtn.addEventListener('click', (e) => {
        e.preventDefault();
        markAsRead('all');
    });

    // Initial fetch
    fetchNotifications();

    // Hide dropdown if clicking outside
    document.addEventListener('click', (e) => {
        if (!dropdown.contains(e.target) && !bellIcon.contains(e.target)) {
            dropdown.classList.remove('active');
        }
    });
}