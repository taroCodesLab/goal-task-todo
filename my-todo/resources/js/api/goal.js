import { deleteJson, postJson, putJson } from "../utils/http";

export async function deleteGoal(id) {
    return deleteJson(`/goal/${id}`);
}

export async function updateOrder(goals) {
    return postJson(`/goal/update-order`, {
        order: goals.map(goal => ({
            id: goal.id,
            order: goal.order
        }))
    });
}

export async function updateGoal({goal_id, goal_goal}) {
    return putJson(`/goal/${goal_id}`, {
        goal: goal_goal
    });
}