

const defaultHeaders = {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
};

export async function getJson(url) {
    const response = await fetch(url, {
        method: 'GET',
        headers: defaultHeaders
    });
    return handleResponse(response);
}

export async function postJson(url, data) {
    const response = await fetch(url, {
        method: 'POST',
        headers: defaultHeaders,
        body: JSON.stringify(data)
    });
    return handleResponse(response);
}

export async function putJson(url, data) {
    const response = await fetch(url, {
        method: 'PUT',
        headers: defaultHeaders,
        body: JSON.stringify(data)
    });
    return handleResponse(response);
}

export async function deleteJson(url) {
    const response = await fetch(url, {
        method: 'DELETE',
        headers: defaultHeaders
    });
    return handleResponse(response);
}

async function handleResponse(response) {
    if (!response.ok) {
        throw new Error(`HTTP Error! Status: ${response.status}`);
    }

    const contentType = response.headers.get('content-type');
    if (!contentType || contentType.indexOf('application/json') === -1) {
        return null;
    }
    
    return await response.json();
}