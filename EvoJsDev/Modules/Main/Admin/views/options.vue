<template>
    <Restricted access="1">
        <h2>Software Options</h2>
        <div class="row">
            <div class="col-md-6">
                <div class="card card-primary">
                    <div class="card-header">
                        App Identity
                    </div>
                    <div class="card-body">
                        <SetConfig :fields="app_identity"></SetConfig>
                    </div>
                </div>
                <div class="card card-primary">
                    <div class="card-header">
                        User registration options
                    </div>
                    <div class="card-body">
                        <SetOption :fields="registration_options"></SetOption>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-primary">
                    <div class="card-header">
                        Mail options
                    </div>
                    <div class="card-body">
                        <SetOption :fields="mail_options"></SetOption>
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
    import { useConfigStore } from '@/store/config'
    import { computed } from 'vue'
    const configStore = useConfigStore();
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
            options: configStore.roles
        }
    ])
    const mail_options = [
        {
            label: "Activate SMTP", 
            name: "activate_smtp",
            as: "radio",
            options: [
                {name: "Yes", value: true},
                {name: "No", value: false}
            ],
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
    ]
    const app_identity = [
        {
            label: 'App Name', 
            name: 'site_name'
        },
        {
            label: 'Main website', 
            name: 'main_website'
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
            label: "Site logo",
            name: "logo",
            as: "croppie",
            enableResize: true
        },
        {
            label: "Site logo 2",
            name: "logo2",
            as: "croppie",
            enableResize: true
        },
        {
            label: "Site icon",
            name: "favicon",
            as: "croppie"
        }
    ]
</script>

<style lang="scss" scoped>

</style>