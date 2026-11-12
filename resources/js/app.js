//

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';

/**
 * Send a request to the API as the logged in user.
 * Takes a plain object (sent as JSON) or FormData, and resolves to `{ ok, data }`.
 */
window.sendJson = async (url, { method = 'POST', body } = {}) => {
    const isForm = body instanceof FormData;
    const response = await fetch(url, {
        method,
        headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            ...(isForm ? {} : { 'Content-Type': 'application/json' }),
        },
        body: isForm ? body : JSON.stringify(body),
    });

    return { ok: response.ok, data: await response.json() };
};
