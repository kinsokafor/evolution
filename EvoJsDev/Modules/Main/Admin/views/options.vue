<template>
    <Restricted access="1">
        <h2>Software Options</h2>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        App Identity
                    </div>
                    <div class="card-body">
                        <SetConfig :fields="app_identity"></SetConfig>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        User registration options
                    </div>
                    <div class="card-body">
                        <SetOption :fields="registration_options"></SetOption>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Mail options
                    </div>
                    <div class="card-body">
                        <SetOption :fields="mail_options"></SetOption>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        SMS options
                    </div>
                    <div class="card-body">
                        <SetOption :fields="sms_options"></SetOption>
                    </div>
                </div>
            </div>
        </div>
        
    </Restricted>
</template>

<script setup>
    import Restricted from '@/components/Restricted.vue'
    import SetOption from '@/components/form/SetOption.vue'
    import SetConfig from '@/components/form/SetConfig.vue'
    import { computed } from 'vue'
    import config from "/config.json"
    import {useConfigStore} from "@/store/config"

    const store = useConfigStore()

    const roles = computed(() => {
        return Object.entries(store.get("Auth.roles")).map(role => {
                return {
                    name: role[1].name,
                    value: role[0],
                    capacity: role[1].capacity,
                }
            })
    })

    const height = computed(() => store.get("logoHeight") ?? 69);
    const width = computed(() => store.get("logoWidth") ?? 200);

    const registration_options = computed(() => [
        {
            label: 'Username Prefix', 
            name: 'username_prefix'
        },
        {
            label: "Pre-activate all registrations?", 
            name: "preactivate_all_registration",
            as: "radio",
            options: [
                {name: "Yes", value: true},
                {name: "No", value: false}
            ],
            class: "pr-2"
        },
        {
            label: "Use unverified role?", 
            name: "use_unverified_role",
            as: "radio",
            options: [
                {name: "Yes", value: true},
                {name: "No", value: false}
            ],
            class: "pr-2"
        },
        {
            label: "Default user role", 
            name: "default_user_role",
            as: "select",
            options: roles.value
        }
    ])
    const mail_options = computed(() => [
        {
            label: "Activate SMTP", 
            name: "activate_smtp",
            as: "checkbox",
            class: "pr-2 pl-2",
            layout: "linear"
        },
        {label: "SMTP sender's name", name: "smtp_name"},
        {label: "SMTP email", name: "smtp_email"},
        {label: "SMTP host", name: "smtp_host"},
        {label: "SMTP port", name: "smtp_port"},
        {label: "SMTP username", name: "smtp_username"},
        {label: "SMTP password", name: "smtp_password", as: "password"},
        {
            label: "Mialer", 
            name: "mailer",
            as: "radio",
            options: [
                {name: "SMTP", value: "smtp"},
                {name: "Sendmail", value: "sendmail"},
                {name: "Q-mail", value: "qmail"},
                {name: "Mail", value: "mail"},
                {name: "Elastic Email", value: "elastic"}
            ],
            class: "pr-2 pl-2",
            layout: "linear"
        }
    ])
    const sms_options = computed(() => [
        {
            label: "Activate SMS", 
            name: "activate_sms",
            as: "checkbox",
            class: "pr-2 pl-2 switch",
            layout: "linear"
        },
        {label: "SMS sender's name", name: "sms_sender"},
        {label: "SMS username", name: "sms_username"},
        {label: "SMS password", name: "sms_password", as: "password"}
    ])
    const app_identity = computed(() => [
        {
            label: 'App Name', 
            name: 'site_name'
        },
        {
            label: 'Main website', 
            name: 'main_website'
        },
        {
            label: 'Timezone', 
            name: 'timezone'
        },
        {
            label: 'Address', 
            name: 'address',
            as: 'textarea'
        },
        {
            label: 'Email address', 
            name: 'email'
        },
        {
            label: 'Phone number', 
            name: 'phone'
        },
        {
            label: 'Mode', 
            name: 'mode',
            as: "select",
            options: ["development", "production"]
        },
        {
            label: "Hosted logo for emails",
            name: "hosted_logo"
        },
        {
            label: "Site logo",
            name: "logo",
            as: "croppie",
            enableResize: true,
            viewport: {
                width: width.value,
                height: height.value
            },
            boundary: {
                width: width.value + 20,
                height: height.value + 20
            }
        },
        {
            label: "Site icon",
            name: "favicon",
            as: "croppie"
        }
    ])
</script>

<style lang="scss" scoped>
    .card {
        background-color: var(--highlight1)
    }
</style>