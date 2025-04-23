import { deleteJson, postJson, putJson } from "../utils/http";

export async function createTask(goalId, task) {
    return postJson(`/task`, {
        goal_id: goalId,
        task: task
    });
}

export async function toggleTaskStatus(nextStatus, task_id) {
    return putJson(`/task/${task_id}/status`, {
        status: nextStatus
    });
}

export async function deleteTask(taskId) {
    return deleteJson(`/task/${taskId}`);
}