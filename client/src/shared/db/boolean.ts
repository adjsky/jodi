export function to_db_boolean(value: boolean): 0 | 1 {
    return value ? 1 : 0;
}

export function from_db_boolean(value: 0 | 1): boolean {
    return value === 1;
}
