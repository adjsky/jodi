export function toDbBoolean(value: boolean): 0 | 1 {
    return value ? 1 : 0;
}

export function fromDbBoolean(value: 0 | 1): boolean {
    return value === 1;
}
