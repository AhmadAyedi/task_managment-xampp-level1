// Add an event listener that executes the function once the HTML document has been fully loaded and parsed
document.addEventListener("DOMContentLoaded", function() {
    // Get the form element with the ID 'addTaskForm'
    const addTaskForm = document.getElementById('addTaskForm');
    // Get the list element with the ID 'tasksList' where tasks will be displayed
    const tasksList = document.getElementById('tasksList');

    // Function to fetch tasks from the server and update the UI
    function fetchTasks() {
        // Make a GET request to the server-side script that retrieves tasks
        fetch('../server_side/tasks.php')
            // Convert the response from the server into a JSON object
            .then(response => response.json())
            // Handle the tasks data received from the server
            .then(tasks => {
                // Clear the current contents of the tasksList element
                tasksList.innerHTML = '';
                // Iterate over each task in the tasks array
                tasks.forEach(task => {
                    // Create a new list item element for each task
                    const li = document.createElement('li');
                    // Set the inner HTML of the list item to include task details and action buttons
                    li.innerHTML = `
                        ${task.task} <!-- Display the task description -->
                        ${task.is_completed ? '<span>(Completed)</span>' : ''} <!-- Show "(Completed)" if the task is completed -->
                        <button onclick="markTask(${task.id})" ${task.is_completed ? 'disabled' : ''}>Mark as Completed</button> <!-- Button to mark task as completed -->
                        <button onclick="deleteTask(${task.id})">Delete</button> <!-- Button to delete the task -->
                    `;
                    // Append the new list item to the tasksList element
                    tasksList.appendChild(li);
                });
            });
    }

    // Handle adding a new task when the form is submitted
    addTaskForm.addEventListener('submit', function(event) {
        // Prevent the default form submission behavior (page reload)
        event.preventDefault();
        // Get the value of the input field named 'task' from the form
        const task = addTaskForm.querySelector('input[name="task"]').value;

        // Make a POST request to the server-side script to add a new task
        fetch('../server_side/actions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded', // Specify the content type of the request
            },
            body: new URLSearchParams({
                action: 'add', // Action to be performed by the server-side script
                task: task // Task data to be sent to the server
            })
        })
        // Handle the response after the task is added
        .then(() => {
            // Reset the form fields
            addTaskForm.reset();
            // Fetch and update the task list to include the newly added task
            fetchTasks();
        });
    });

    // Function to mark a task as completed
    window.markTask = function(taskId) {
        // Make a POST request to the server-side script to mark the task as completed
        fetch('../server_side/actions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded', // Specify the content type of the request
            },
            body: new URLSearchParams({
                action: 'mark', // Action to be performed by the server-side script
                task_id: taskId // ID of the task to be marked as completed
            })
        })
        // Fetch and update the task list after marking the task as completed
        .then(() => fetchTasks());
    };

    // Function to delete a task
    window.deleteTask = function(taskId) {
        // Make a POST request to the server-side script to delete the task
        fetch('../server_side/actions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded', // Specify the content type of the request
            },
            body: new URLSearchParams({
                action: 'delete', // Action to be performed by the server-side script
                task_id: taskId // ID of the task to be deleted
            })
        })
        // Fetch and update the task list after deleting the task
        .then(() => fetchTasks());
    };

    // Initial fetch of tasks when the page loads
    fetchTasks();
});
