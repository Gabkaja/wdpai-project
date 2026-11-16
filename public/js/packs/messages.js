export function messageFor(error) {
    switch (error.code) {
        case 'insufficient_points':
            return 'Not enough points.';
        case 'no_unopened_packs':
            return 'You have no unopened packs.';
        case 'invalid_quantity':
            return 'Quantity is out of the allowed range.';
        case 'validation_failed':
            return 'Validation failed.';
        case 'csrf_failed':
            return 'Invalid CSRF token. Please refresh the page and try again.';
        case 'unauthenticated':
            return 'Please log in. Visit /login.';
        case 'network_error':
            return 'Network error. Please try again.';
        case 'timeout':
            return 'Request timed out. Please try again.';
        case 'internal_error':
            return 'An internal error occurred.';
        case 'bad_response':
            return 'Invalid server response.';
        default:
            return error.message || 'An error occurred.';
    }
}

export function fieldsMessage(error) {
    if (error.code === 'validation_failed' && error.fields && typeof error.fields === 'object') {
        const parts = [];
        for (const [field, msg] of Object.entries(error.fields)) {
            parts.push(`${field}: ${msg}`);
        }
        if (parts.length > 0) {
            return parts.join(' ');
        }
    }
    return null;
}
