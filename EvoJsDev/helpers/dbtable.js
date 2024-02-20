import axios from "axios";
import {nonce} from "./functions"

export class dbTable {

    controller = new AbortController();

    async get(table, filter = {}) {
        const signal = this.controller.signal
        const link = this.buildQuery(process.env.EVO_API_URL + `/api/dbtable/${table}/`, filter);
        return await axios.get(link, {
            'Access-Control-Allow-Credentials':true,
            headers: {
                'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            },
            signal
        })
    }

    async update(table, id, data = {}) {
        return await axios.put(process.env.EVO_API_URL + `/api/dbtable/${table}/id/${id}`, data, {
            'Access-Control-Allow-Credentials':true,
            headers: {
                'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            }
        });
    } 

    async delete(table, filter = {}) {
        const link = this.buildQuery(process.env.EVO_API_URL + `/api/dbtable/${table}/`, filter);
        return await axios.delete(link, {
            'Access-Control-Allow-Credentials':true,
            headers: {
                'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            }
        });
    } 

    async new(table, data = {}) {
        return await axios.post(process.env.EVO_API_URL + `/api/dbtable/${table}/`, data, {
            'Access-Control-Allow-Credentials':true,
            headers: {
                'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            }
        });
    }

    abort() {
        this.controller.abort()
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
    
}