document.addEventListener("DOMContentLoaded", function() {
    const addTaskForm = document.getElementById('addTaskForm');
    const tasksList = document.getElementById('tasksList');

    // Function to fetch tasks and update the UI
    function fetchTasks() {
        fetch('../server_side/tasks.php')
            .then(response => response.json())
            .then(tasks => {
                tasksList.innerHTML = '';
                tasks.forEach(task => {
                    const li = document.createElement('li');
                    li.innerHTML = `
                        <span>${task.task}</span>
                        ${task.is_completed ? '<span class="completed">(Completed)</span>' : ''}
                        <button class="mark-btn" onclick="markTask(${task.id})" ${task.is_completed ? 'disabled' : ''}>Mark as Completed</button>
                        <button class="delete-btn" onclick="deleteTask(${task.id})">Delete</button>
                    `;
                    tasksList.appendChild(li);
                });
            })
            .catch(error => console.error('Error fetching tasks:', error));
    }

    // Handle adding a new task
    addTaskForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const task = addTaskForm.querySelector('input[name="task"]').value;

        fetch('../server_side/actions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'add',
                task: task
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addTaskForm.reset();
                fetchTasks();
            } else {
                alert('Error adding task.');
            }
        })
        .catch(error => console.error('Error adding task:', error));
    });

    // Function to mark a task as completed
    window.markTask = function(taskId) {
        fetch('../server_side/actions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'mark',
                task_id: taskId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                fetchTasks();
            } else {
                alert('Error marking task.');
            }
        })
        .catch(error => console.error('Error marking task:', error));
    };

    // Function to delete a task
    window.deleteTask = function(taskId) {
        if (confirm('Are you sure you want to delete this task?')) {
            fetch('../server_side/actions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'delete',
                    task_id: taskId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    fetchTasks();
                } else {
                    alert('Error deleting task.');
                }
            })
            .catch(error => console.error('Error deleting task:', error));
        }
    };

    // Initial fetch of tasks
    fetchTasks();
});
