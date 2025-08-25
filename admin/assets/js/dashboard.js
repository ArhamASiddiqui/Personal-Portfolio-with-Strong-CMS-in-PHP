document.addEventListener('DOMContentLoaded', function() {
    // The fetch URL is an absolute path from the root of your website.
    fetch('/my_portfolio_cms/api/dashboard_data.php')
        .then(response => {
            if (!response.ok) {
                // If the response is not a 200 OK, throw an error
                throw new Error('Network response was not ok. Status: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            // Check if the data is valid before using it
            if (data) {
                // Update the card values
                document.getElementById('totalProjects').textContent = data.totalProjects;
                document.getElementById('totalMessages').textContent = data.totalMessages;
                document.getElementById('totalSkills').textContent = data.totalSkills;

                // Prepare data for the Project Types chart
                const projectLabels = data.projectTypes.map(item => item.type);
                const projectCounts = data.projectTypes.map(item => item.count);

                const projectTypesCtx = document.getElementById('projectTypesChart').getContext('2d');
                new Chart(projectTypesCtx, {
                    type: 'doughnut',
                    data: {
                        labels: projectLabels,
                        datasets: [{
                            data: projectCounts,
                            backgroundColor: [
                                '#7b68ee', // Purple
                                '#ff9900', // Orange
                                '#2ecc71', // Green
                            ],
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            }
                        }
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error fetching dashboard data:', error);
            // Display a user-friendly error message on the cards
            document.getElementById('totalProjects').textContent = 'Error';
            document.getElementById('totalMessages').textContent = 'Error';
            document.getElementById('totalSkills').textContent = 'Error';
        });
});