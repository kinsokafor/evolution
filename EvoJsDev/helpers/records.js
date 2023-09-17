import axios from "axios";
import {nonce} from "./functions"

export class Records {

    async get(record) {
        return await axios.get(process.env.EVO_API_URL + "/api/records/"+record, {
            withCredentials: true,
            headers: {
                'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            }
        })
    }

    async update(record, value) {
        return await axios.put(process.env.EVO_API_URL + "/api/records/"+record, {value: value}, {
            withCredentials: true,
            headers: {
                'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            }
        });
    } 

    async delete(record) {
        return await axios.delete(process.env.EVO_API_URL + "/api/records/"+record, {
            withCredentials: true,
            headers: {
                'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            }
        });
    } 

    async new(record, value) {
        return await this.update(record, value)
    }
    
}