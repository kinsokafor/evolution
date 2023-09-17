import axios from "axios";
import {nonce} from "./functions"

export class Options {

    async get(option) {
        return await axios.get(process.env.EVO_API_URL + "/api/options/"+option, {
            withCredentials: true,
            headers: {
                'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            }
        })
    }

    async update(option, value) {
        return await axios.put(process.env.EVO_API_URL + "/api/options/"+option, {value: value}, {
            withCredentials: true,
            headers: {
                'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            }
        });
    } 

    async delete(option) {
        return await axios.delete(process.env.EVO_API_URL + "/api/options/"+option, {
            withCredentials: true,
            headers: {
                'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            }
        });
    } 

    async new(option, value) {
        return await this.update(option, value)
    }
    
}