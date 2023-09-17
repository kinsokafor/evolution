import {defineStore} from 'pinia';
import {randomId} from '@/helpers';

export const useAlertStore = defineStore('useAlertStore', {
    state: () => {
        return {
            alerts: []
        }
    },
    actions: {
        add(message, bg = "primary", y = "top", x = "right") {
            const key = randomId(10);
            const len = this.alerts.push({
                message: message,
                bg: bg,
                y: y,
                x: x,
                key: key
            })
            setTimeout(() => {
                delete this.alerts[len - 1]
            }, 12000)
        },
        close(key) {
            const indexOfObject = this.alerts.findIndex(alert => {
                return alert.key === key;
            });
            this.alerts.splice(indexOfObject, 1);
        }
    },
    getters: {
        getAlerts: (state) => {
            return state.alerts
        },

        getTopRight: (state) => {
            return state.alerts.filter((alert) => {
                return alert.y == 'top' && alert.x == 'right';
            })
        },

        getTopCenter: (state) => {
            return state.alerts.filter((alert) => {
                return alert.y == 'top' && alert.x == 'center';
            })
        },

        getTopLeft: (state) => {
            return state.alerts.filter((alert) => {
                return alert.y == 'top' && alert.x == 'left';
            })
        },

        getCenterRight: (state) => {
            return state.alerts.filter((alert) => {
                return alert.y === 'center' && alert.x === 'right';
            })
        },

        getCenterCenter: (state) => {
           return state.alerts.filter((alert) => {
                return alert.y == 'center' && alert.x == 'center';
            }) 
        },

        getCenterLeft: (state) => {
           return state.alerts.filter((alert) => {
                return alert.y == 'center' && alert.x == 'left';
            }) 
        },

        getBottomRight: (state) => {
            return state.alerts.filter((alert) => {
                return alert.y == 'bottom' && alert.x == 'right';
            })
        },

        getBottomCenter: (state) => {
            return state.alerts.filter((alert) => {
                return alert.y == 'bottom' && alert.x == 'center';
            })
        },

        getBottomLeft: (state) => {
            return state.alerts.filter((alert) => {
                return alert.y == 'bottom' && alert.x == 'left';
            })
        }
    }
})