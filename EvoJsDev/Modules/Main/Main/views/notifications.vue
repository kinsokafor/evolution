<template>
    <div>
        <InfiniteScroll :data="store.get" v-slot="{data}">
            <ListItem v-for="item in data" :key="item.id" v-memo="item">
                <p>{{ item.content }}</p>
                <template #right>
                    <span class="small">{{ getDate(item.last_sent) }}</span>
                    <p :class="{'text-success': (item.status = 'read'), 'text-danger': (item.status = 'unread')}">{{ item.status }}</p>
                </template>
            </ListItem>
        </InfiniteScroll>
    </div>
</template>

<script setup>
    import {useNotificationsStore} from '../../store/notifications'
    import ListItem from '@/components/theme/ListItem.vue'
    import InfiniteScroll from '@/components/InfiniteScroll.vue';

    const store = useNotificationsStore()

    const getDate = (timestamp) => {
        const d = new Date(timestamp * 1000)
        return `${d.getDate()}/${d.getMonth() + 1}/${d.getFullYear()}`
    }
</script>

<style lang="scss" scoped>
    * {
        margin: 0;
        padding: 0;
    }
</style>