document.addEventListener('DOMContentLoaded', () => {
    fetchTasks();
});

function addTask() {
    const taskInput = document.getElementById('taskInput');
    const deadlineInput = document.getElementById('deadlineInput');
    const task = taskInput.value.trim();
    const deadline = deadlineInput.value;

    if (task && deadline) {
        fetch('http://localhost/task_management/server_side/add_task.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ task, deadline })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                taskInput.value = '';
                deadlineInput.value = '';
                fetchTasks();
            } else {
                alert('Failed to add task');
            }
        });
    } else {
        alert('Please enter a task and deadline');
    }
}

function fetchTasks() {
    fetch('http://localhost/task_management/server_side/fetch_tasks.php')
    .then(response => response.json())
    .then(data => {
        const tasksDiv = document.getElementById('tasks');
        tasksDiv.innerHTML = '';
        data.forEach(task => {
            const taskDiv = document.createElement('div');
            taskDiv.className = 'task';
            if (task.completed) {
                taskDiv.classList.add('completed');
            }
            taskDiv.innerHTML = `
                <span>${task.task}</span>
                <span class="deadline">Due: ${task.deadline}</span>
                <div>
                    <button onclick="editTask(${task.id}, ${task.completed})" class="${task.completed ? 'completed' : ''}">
                        ${task.completed ? 'Task Achieved' : 'Task Not Achieved'}
                    </button>
                    <button onclick="deleteTask(${task.id})">Delete</button>
                </div>
            `;
            tasksDiv.appendChild(taskDiv);
        });
    });
}

function editTask(id, completed) {
    fetch('http://localhost/task_management/server_side/edit_task.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id, completed: !completed })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            fetchTasks();
        } else {
            alert('Failed to edit task');
        }
    });
}

function deleteTask(id) {
    fetch('http://localhost/task_management/server_side/delete_task.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            fetchTasks();
        } else {
            alert('Failed to delete task');
        }
    });
}
