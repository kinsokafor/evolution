import axios from "axios";
import {nonce} from "./functions"

export class Config {

    async get(key) {
        return await axios.post(process.env.EVO_API_URL + "/api/config/"+key, {key: key}, {
            'Access-Control-Allow-Credentials':true,
            headers: {
                'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            }
        })
    }

    async update(values) {
        return await axios.post(process.env.EVO_API_URL + "/api/config/", values, {
            'Access-Control-Allow-Credentials':true,
            headers: {
                'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            }
        });
    } 

    async delete(key) {
        return await axios.delete(process.env.EVO_API_URL + "/api/config/"+key, {
            'Access-Control-Allow-Credentials':true,
            headers: {
                'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            }
        });
    } 

    async new(values) {
        return await this.update(values)
    }
    
}