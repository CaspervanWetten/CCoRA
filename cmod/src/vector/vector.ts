export default class Vector {
    protected data: Array<number>;

    public constructor(d: number = 1) {
        this.data = new Array<number>();
        for(let i = 0; i < d; i++) {
            this.data.push(0);
        }
    }

    public get(i: number): number {
        return this.data[i];
    }

    public set(i: number, val: number): void {
        this.data[i] = val;
    }

    public get dimensions(): number {
        return this.data.length;
    }

    public static add(lhs: Vector, rhs: Vector): Vector {
        if(lhs.dimensions == rhs.dimensions) {
            let v = new Vector(lhs.dimensions);
            for(let i = 0; i < lhs.dimensions; i++) {
                v.set(i, lhs.get(i) + rhs.get(i));
            }
            return v;
        }
        throw new Error("Vectors not of equal dimensions");
    }

    public static sub(lhs: Vector, rhs: Vector): Vector {
        return Vector.add(lhs, Vector.scale(rhs, -1));
    }

    public static scale(v: Vector, n: number): Vector {
        let result = new Vector(v.dimensions);
        for(let i = 0; i < v.dimensions; i++) {
            result.set(i,v.get(i) * n);
        }
        return result;
    }

    public static norm(v: Vector): number {
        let sum = 0;
        for(let i = 0; i < v.dimensions; i++) {
            sum += v.get(i) * v.get(i);
        }
        return Math.sqrt(sum);
    }

    public static unit(v: Vector): Vector {
        let norm = Vector.norm(v);
        return Vector.scale(v, 1 / norm);
    }
}
