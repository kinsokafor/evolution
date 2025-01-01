import axios from "axios";
import {nonce} from "./functions"

export class Records {

    controller = new AbortController();

    async get(record) {
        const signal = this.controller.signal
        return await axios.get(process.env.EVO_API_URL + "/api/records/"+record, {
            'Access-Control-Allow-Credentials':true,
            headers: {
                // 'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            },
            signal
        })
    }

    async update(record, value) {
        return await axios.put(process.env.EVO_API_URL + "/api/records/"+record, {value: value}, {
            'Access-Control-Allow-Credentials':true,
            headers: {
                // 'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            }
        });
    } 

    async delete(record) {
        return await axios.delete(process.env.EVO_API_URL + "/api/records/"+record, {
            'Access-Control-Allow-Credentials':true,
            headers: {
                // 'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            }
        });
    } 

    async new(record, value) {
        return await this.update(record, value)
    }

    abort() {
        this.controller.abort()
    }
    
}