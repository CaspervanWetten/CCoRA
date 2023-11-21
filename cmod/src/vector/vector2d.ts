import Vector from "./vector";

export default class Vector2D {
    public vec: Vector;

    public constructor(x: number, y: number) {
        this.vec = new Vector(2);
        this.vec.set(0, x);
        this.vec.set(1, y);
    }

    get x(): number {
        return this.vec.get(0);
    }

    set x(val: number) {
        this.vec.set(0, val);
    }

    get y(): number {
        return this.vec.get(1);
    }

    set y(val: number) {
        this.vec.set(1,val);
    }

    public static equal(lhs: Vector2D, rhs: Vector2D): boolean {
        return lhs.x === rhs.x && lhs.y === rhs.y;
    }

    public static add(lhs: Vector2D, rhs: Vector2D): Vector2D {
        let vec = Vector.add(lhs.vec, rhs.vec);
        return new Vector2D(vec.get(0), vec.get(1));
    }

    public static sub(lhs: Vector2D, rhs: Vector2D): Vector2D {
        let rhss = Vector2D.scale(rhs, -1);
        return Vector2D.add(lhs, rhss);
    }

    public static scale(v: Vector2D, t: number): Vector2D {
        let r = Vector.scale(v.vec, t);
        return new Vector2D(r.get(0), r.get(1));
    }

    public static norm(v: Vector2D): number {
        return Vector.norm(v.vec);
    }

    public static unit(v: Vector2D): Vector2D {
        let norm = Vector2D.norm(v);
        return Vector2D.scale(v, 1 / norm);
    }

    public static angle(a: Vector2D, b: Vector2D): number {
        let vec = Vector2D.sub(b, a);
        let ratio = -vec.y / vec.x;
        let angle = Math.atan(ratio);
        if (angle == 0 && a.x > b.x) {
            angle = Math.PI;
        }
        if (angle < 0) {
            angle = Math.PI + angle;
        }
        if (a.y < b.y) {
            angle += Math.PI;
        }
        return angle;
    }
}
