import api from '../axios';


export async function createTask(goalId, task) {
    const response = await api.post(`/api/goals/${goalId}/tasks`, {
        goal_id: goalId,
        task: task,
    });

    return response.data;
}

export async function toggleTaskStatus(nextStatus, taskId, goalId) {
    const response = await api.patch(`/api/goals/${goalId}/tasks/${taskId}/status`, {
        status: nextStatus,
    });

    return response.data;
}

export async function deleteTask(goalId, taskId) {
    const response = await api.delete(`/api/goals/${goalId}/tasks/${taskId}`);

    return response.data;
}