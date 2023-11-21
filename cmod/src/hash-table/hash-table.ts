export default class HashTable<K, V> {
    protected table: Map<K, V>;

    public constructor() {
        this.table = new Map<K, V>();
    }

    public get(key: K): V {
        return this.table.get(key);
    }

    public set(key: K, val: V): void {
        this.table.set(key, val);
    }

    public has(key: K): boolean {
        return this.table.has(key);
    }

    public remove(key: K): void {
        this.table.delete(key);
    }

    public each(f: (k: K, v: V) => void): void {
        this.table.forEach((a: V, b: K) => {
            f(b, a);
        });
    }

    public get keys() {
        return Array.from(this.table.keys());
    }

    public get values() {
        return Array.from(this.table.values());
    }

    public filterKeys(f: (k: K) => boolean): HashTable<K, V> {
        let result = new HashTable<K, V>();
        this.each((key: K, value: V) => {
            if (f(key)) {
                result.set(key, value);
            }
        });
        return result;
    }

    public filterValues(f: (v: V) => boolean): HashTable<K, V> {
        let result = new HashTable<K, V>();
        this.each((key: K, value: V) => {
            if (f(value)) {
                result.set(key, value);
            }
        });
        return result;
    }

    public isEmpty() {
        return this.size <= 0;
    }

    public get size() {
        return this.table.size;
    }
}
