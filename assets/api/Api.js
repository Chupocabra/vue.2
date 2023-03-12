import axios from 'axios';

const config = {
    headers: { 'Content-Type': 'application/json', 'Access-Control-Allow-Origin': '*' },
    timeout: 30000,
};

export class Api {
    static async post(url, data) {
        return new Promise((resolve, reject) => {
            axios
                .post(url, data)
                .then(
                    response => resolve(response),
                    err => {
                        reject(err);
                    },
                )
                .catch(error => reject(error));
        });
    }

    static async get(url, params) {
        return new Promise((resolve, reject) => {
            axios
                .get(url)
                .then(
                    response => resolve(response.data),
                    err => {
                        reject(err);
                    },
                )
                .catch(error => reject(error));
        });
    }
}
