import { deleteJson, postJson, putJson } from "../utils/http";
import api from '../axios';

// export async function createTask(goalId, task) {
//     return postJson(`/task`, {
//         goal_id: goalId,
//         task: task
//     });
// }

// export async function toggleTaskStatus(nextStatus, task_id) {
//     return putJson(`/task/${task_id}/status`, {
//         status: nextStatus
//     });
// }

// export async function deleteTask(taskId) {
//     return deleteJson(`/task/${taskId}`);
// }

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