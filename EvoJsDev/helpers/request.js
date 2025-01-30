import axios from "axios";
import {nonce} from "./functions"

export class Request {

    root = process.env.EVO_API_URL;

    controller = new AbortController();

    async get(endpoint, filter = {}) {
        const signal = this.controller.signal
        const link = this.buildQuery(endpoint, filter);
        return await axios.get(link, {
            'Access-Control-Allow-Credentials':true,
            headers: {
                // 'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            },
            signal
        })
    }

    async post(endpoint, data = {}) {
        return await axios.post(endpoint, data, {
            'Access-Control-Allow-Credentials':true,
            headers: {
                // 'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            }
        })
    }

    async put(endpoint, data = {}) {
        return await axios.put(endpoint, data, {
            'Access-Control-Allow-Credentials':true,
            headers: {
                // 'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            }
        })
    }

    async delete(endpoint) {
        return await axios.delete(endpoint, {
            'Access-Control-Allow-Credentials':true,
            headers: {
                // 'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            }
        })
    }

    buildQuery(base, filter = {}) {
        const link = new URL(base);
        for (const key in filter) {
            if (Object.hasOwnProperty.call(filter, key)) {
                const value = filter[key];
                link.searchParams.append(key, value);
            }
        }
        return link;
    }

    abort() {
        this.controller.abort()
    }
}