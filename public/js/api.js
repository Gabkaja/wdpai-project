class ApiError extends Error {
    constructor(status, code, message, fields = {}) {
        super(message);
        this.status = status;
        this.code = code;
        this.fields = fields;
    }
}

function headers() {
    const tokenEl = document.querySelector('meta[name="csrf-token"]');
    const result = {
        Accept: 'application/json',
        'Content-Type': 'application/json',
    };

    if (tokenEl) {
        result['X-CSRF-Token'] = tokenEl.content;
    }

    return result;
}

async function request(url, options) {
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 10000);

    try {
        const response = await fetch(url, {
            ...options,
            headers: { ...headers(), ...(options.headers || {}) },
            credentials: 'same-origin',
            signal: controller.signal,
        });

        clearTimeout(timeoutId);

        const text = await response.text();
        let data;

        try {
            data = JSON.parse(text);
        } catch (error) {
            throw new ApiError(response.status, 'bad_response', 'Server returned an invalid response.');
        }

        if (!response.ok) {
            const error = data && data.error ? data.error : {};
            throw new ApiError(
                response.status,
                error.code || 'internal_error',
                error.message || 'An error occurred.',
                error.fields || {}
            );
        }

        if (!data || data.ok !== true || data.data === undefined) {
            throw new ApiError(response.status, 'bad_response', 'Server returned an invalid response.');
        }

        return data.data;
    } catch (error) {
        clearTimeout(timeoutId);

        if (error instanceof ApiError) {
            throw error;
        }

        if (error.name === 'AbortError') {
            throw new ApiError(0, 'timeout', 'Request timed out.');
        }

        throw new ApiError(0, 'network_error', 'Network error.');
    }
}

export const api = {
    get(url) {
        return request(url, { method: 'GET' });
    },

    post(url, body) {
        return request(url, { method: 'POST', body: JSON.stringify(body) });
    },
};