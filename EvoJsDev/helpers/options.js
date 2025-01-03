import axios from "axios";
import {nonce} from "./functions"

export class Options {

    controller = new AbortController();

    async get(option) {
        const signal = this.controller.signal
        return await axios.get(process.env.EVO_API_URL + "/api/options/"+option, {
            'Access-Control-Allow-Credentials':true,
            headers: {
                // 'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            },
            signal
        })
    }

    async update(option, value) {
        return await axios.put(process.env.EVO_API_URL + "/api/options/"+option, {value: value}, {
            'Access-Control-Allow-Credentials':true,
            headers: {
                // 'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            }
        });
    } 

    async delete(option) {
        return await axios.delete(process.env.EVO_API_URL + "/api/options/"+option, {
            'Access-Control-Allow-Credentials':true,
            headers: {
                // 'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            }
        });
    } 

    async new(option, value) {
        return await this.update(option, value)
    }

    abort() {
        this.controller.abort()
    }
    
}