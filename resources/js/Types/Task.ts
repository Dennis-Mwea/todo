import type {UserTask} from "@/Types/UserTask";
import type {Status} from "@/Types/Status";

export interface Task {
    id: number
    name: string
    due_date: string
    status_id: number | string
    deleted_at: string
    updated_at: string
    created_at: string
    description: string
    task: UserTask
    status: Status
    formatted_due_date: string
}
