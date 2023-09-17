import axios from "axios";
import {nonce} from "./functions"

export class Posts {

    async get(filter = {}, type = false) {
        if(type) {
            filter["type"] = type;
        }
        const link = this.buildQuery(process.env.EVO_API_URL + "/api/post/", filter);
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
        return await axios.put(process.env.EVO_API_URL + '/api/post/id/'+id, data, {
            withCredentials: true,
            headers: {
                'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            }
        });
    } 

    async delete(filter = {}, type = false) {
        if(type) {
            filter["type"] = type;
        }
        const link = this.buildQuery(process.env.EVO_API_URL + "/api/post/", filter);
        return await axios.delete(link, {
            withCredentials: true,
            headers: {
                'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            }
        });
    } 

    async new(type, data = {}) {
        return await axios.post(process.env.EVO_API_URL + '/api/post/'+type, data, {
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