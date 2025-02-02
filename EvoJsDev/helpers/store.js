import axios from "axios";
import {nonce} from "./functions"

export class Store {

    controller = new AbortController();

    async get(filter = {}, type = false) {
        const signal = this.controller.signal
        if(type) {
            filter["type"] = type;
        }
        const link = this.buildQuery(process.env.EVO_API_URL + "/api/store/", filter);
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

    async update(id, data = {}) {
        return await axios.put(process.env.EVO_API_URL + '/api/store/id/'+id, data, {
            'Access-Control-Allow-Credentials':true,
            headers: {
                // 'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            }
        });
    } 

    async delete(filter = {}, type = false) {
        if(type) {
            filter["type"] = type;
        }
        const link = this.buildQuery(process.env.EVO_API_URL + "/api/store/", filter);
        return await axios.delete(link, {
            'Access-Control-Allow-Credentials':true,
            headers: {
                // 'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            }
        });
    } 

    async new(type, data = {}) {
        return await axios.post(process.env.EVO_API_URL + '/api/store/'+type, data, {
            'Access-Control-Allow-Credentials':true,
            headers: {
                // 'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            }
        });
    }

    buildQuery(base, filter = {}) {
        if(filter.id !== undefined) {
            return base + "id/" + parseInt(filter.id)
        }
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