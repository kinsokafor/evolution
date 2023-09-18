import axios from "axios";
import {nonce} from "./functions"

export class Users {

    async get(filter = {}) {
        const link = this.buildQuery(process.env.EVO_API_URL + "/api/user/", filter);
        return await axios.get(link, {
            withCredentials: true,
            headers: {
                'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            }
        })
    }

    async update(id, data = {}) {
        data['id'] = parseInt(id);
        return await axios.put(process.env.EVO_API_URL + '/api/user/id/'+id, data, {
            withCredentials: true,
            headers: {
                'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            }
        });
    } 

    async delete(filter = {}) {
        const link = this.buildQuery(process.env.EVO_API_URL + "/api/user/", filter);
        return await axios.delete(link, {
            withCredentials: true,
            headers: {
                'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            }
        });
    } 

    async new(data = {}, endpoint = null) {
        const url = endpoint == null ? process.env.EVO_API_URL + '/api/user/' : endpoint;
        return await axios.post(url, data, {
            withCredentials: true,
            headers: {
                'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            }
        });
    }

    buildQuery(base, filter = {}) {
        if(filter.id !== undefined) {
            return base + "id/" + parseInt(filter.id)
        }
        if(filter.username !== undefined) {
            return base + "username/" + parseInt(filter.username)
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