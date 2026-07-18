<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { ChevronLeft, ChevronRight } from '@lucide/vue';

const props = defineProps<{
    requests: any[];
}>();

const currentDate = ref(new Date());

const currentMonthName = computed(() => {
    return currentDate.value.toLocaleString('default', { month: 'long', year: 'numeric' });
});

const nextMonth = () => {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1, 1);
};

const prevMonth = () => {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() - 1, 1);
};

// Calendar math
const calendarDays = computed(() => {
    const year = currentDate.value.getFullYear();
    const month = currentDate.value.getMonth();
    
    const firstDay = new Date(year, month, 1).getDay(); // 0 is Sunday
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    
    const days = [];
    
    // Empty padding for first row
    for (let i = 0; i < firstDay; i++) {
        days.push({ day: null, date: null, isToday: false });
    }
    
    const today = new Date();
    
    // Actual days
    for (let i = 1; i <= daysInMonth; i++) {
        const date = new Date(year, month, i);
        // Normalize time for comparison
        const isToday = date.toDateString() === today.toDateString();
        
        days.push({
            day: i,
            date: date,
            isToday: isToday,
        });
    }
    
    return days;
});

const getRequestsForDate = (date: Date | null) => {
    if (!date) return [];
    
    // Create local ISO string avoiding timezone shifts
    const targetDate = new Date(date.getTime() - (date.getTimezoneOffset() * 60000)).toISOString().split('T')[0];
    
    return props.requests.filter(req => {
        const start = req.start_date.split('T')[0];
        const end = req.end_date.split('T')[0];
        return targetDate >= start && targetDate <= end;
    });
};
</script>

<template>
    <div class="flex h-full flex-1 flex-col gap-6 p-4 max-w-7xl mx-auto w-full">
        <Head title="Calendar" />
        
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold tracking-tight text-foreground">Company Calendar</h1>
            <div class="flex items-center space-x-2">
                <Button variant="outline" size="icon" @click="prevMonth"><ChevronLeft class="h-4 w-4"/></Button>
                <span class="text-lg font-semibold min-w-[150px] text-center">{{ currentMonthName }}</span>
                <Button variant="outline" size="icon" @click="nextMonth"><ChevronRight class="h-4 w-4"/></Button>
            </div>
        </div>
        
        <Card>
            <CardContent class="p-0">
                <div class="grid grid-cols-7 border-b border-border text-center font-medium bg-muted/50">
                    <div class="py-3 border-r border-border text-muted-foreground uppercase text-xs">Sun</div>
                    <div class="py-3 border-r border-border text-muted-foreground uppercase text-xs">Mon</div>
                    <div class="py-3 border-r border-border text-muted-foreground uppercase text-xs">Tue</div>
                    <div class="py-3 border-r border-border text-muted-foreground uppercase text-xs">Wed</div>
                    <div class="py-3 border-r border-border text-muted-foreground uppercase text-xs">Thu</div>
                    <div class="py-3 border-r border-border text-muted-foreground uppercase text-xs">Fri</div>
                    <div class="py-3 text-muted-foreground uppercase text-xs">Sat</div>
                </div>
                
                <div class="grid grid-cols-7 auto-rows-[120px]">
                    <div v-for="(day, idx) in calendarDays" :key="idx" 
                         class="border-b border-r border-border relative p-2 overflow-y-auto"
                         :class="{ 'bg-muted/10': !day.day, 'bg-background': day.day }">
                        
                        <div v-if="day.day" class="flex items-center justify-between">
                            <span :class="{'bg-primary text-primary-foreground rounded-full w-6 h-6 flex items-center justify-center text-xs': day.isToday}">
                                {{ day.day }}
                            </span>
                        </div>
                        
                        <div v-if="day.date" class="mt-2 space-y-1">
                            <div v-for="req in getRequestsForDate(day.date)" :key="req.id"
                                 class="text-[10px] px-1.5 py-1 rounded truncate font-medium border"
                                 :class="{
                                     'bg-emerald-500/10 text-emerald-600 border-emerald-500/20': req.status === 'approved',
                                     'bg-yellow-500/10 text-yellow-600 border-yellow-500/20': req.status === 'pending'
                                 }"
                                 :title="req.reason">
                                {{ req.user?.name }}
                            </div>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
