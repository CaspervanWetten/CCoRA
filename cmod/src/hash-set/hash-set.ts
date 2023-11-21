export default class HashSet<T> {
    protected set: Set<T>;

    public constructor() {
        this.set = new Set<T>();
    }

    public add(item: T): void {
        this.set.add(item);
    }

    public remove(item: T): void {
        this.set.delete(item);
    }

    public clear(): void {
        this.set.clear();
    }

    public member(item: T): boolean {
        return this.set.has(item);
    }

    public contains(item: T): boolean {
        return this.member(item);
    }

    public isEmpty(): boolean {
        return this.size <= 0;
    }

    public each(f: (T) => any): void {
        this.set.forEach(f);
    }

    public toArray(): Array<T> {
        return Array.from(this.set.values());
    }

    public get size(): number {
        return this.set.size;
    }
}
