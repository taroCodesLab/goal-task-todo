import { deleteJson, postJson, putJson } from "../utils/http";

export async function deleteTodo(id) {
    return deleteJson(`/todo/${id}`);
}

export async function updateOrder(todos) {
    return postJson(`/todo/update-order`, {
        order: todos.map(todo => ({
            id: todo.id,
            order: todo.order
        }))
    });
}

export async function updateTodo({todo_id, todo_goal}) {
    return putJson(`/todo/${todo_id}`, {
        goal: todo_goal
    });
}