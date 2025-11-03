import api from "../axios";

export async function importGoalsFromGuest(goals) {
    const response = await api.post(`/api/goals/import`, { goals });
    return response.data;
}