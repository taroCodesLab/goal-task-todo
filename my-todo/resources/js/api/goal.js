import { deleteJson, postJson, putJson } from "../utils/http";
import api from '../axios';


// export async function createGoal(goal) {
//     return postJson(`/goal`, {
//         goal: goal
//     });
// }

// export async function deleteGoal(id) {
//     return deleteJson(`/goal/${id}`);
// }

// export async function updateOrder(goals) {
//     return postJson(`/goal/update-order`, {
//         order: goals.map(goal => ({
//             id: goal.id,
//             order: goal.order
//         }))
//     });
// }

// export async function updateGoal({goal_id, goal_goal}) {
//     return putJson(`/goal/${goal_id}`, {
//         goal: goal_goal
//     });
// }

export async function createGoal(goal) {
    const response = await api.post('/api/goals', { goal });
    return response.data;
}

export async function deleteGoal(id) {
    const response = await api.delete(`/api/goals/${id}`);
    return response.data;
}

export async function updateGoal({goal_id, goal_goal}) {
    const response = await api.put(`/api/goals/${goal_id}`, {
        goal: goal_goal
    });

    return response.data;
}

export async function updateOrder(goals) {
    const response = await api.post('/api/goals/reorder', {
        order: goals.map(goal => ({
            id: goal.id,
            order: goal.order
        }))
    });
    return response.data;
}