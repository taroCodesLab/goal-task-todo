import api from "../axios";
import { postJson } from "../utils/http";

// export async function importGoalsFromGuest(goals) {
//     return await postJson('/api/api/goals/import', { goals });
// }

export async function importGoalsFromGuest(goals) {
    const response = await api.post(`/api/goals/import`, { goals });
    return response.data;
}