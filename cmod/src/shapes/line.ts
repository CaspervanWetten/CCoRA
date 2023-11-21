import Shape2D from "./shape2d";
import Vector2D from "src/vector/vector2d";

export default class Line implements Shape2D {
    public readonly source: Vector2D;
    public readonly target: Vector2D;
    public readonly bend: number;

    public constructor(x1: number, y1: number, x2: number, y2: number, bend: number = 0) {
        this.source = new Vector2D(x1, y1);
        this.target = new Vector2D(x2, y2);
        this.bend = bend;
    }

    public stroke(context: CanvasRenderingContext2D): void {
        this.preparePath(context);
        context.stroke();
    }

    public fill(context: CanvasRenderingContext2D): void {
        this.stroke(context);
    }

    public hit(point: Vector2D, context: CanvasRenderingContext2D): boolean {
        context.save();
        context.lineWidth = 20;
        this.preparePath(context);
        let result = context.isPointInStroke(point.x, point.y);
        context.restore();
        return result;
    }

    public getControlPoint(): Vector2D {
        let vec = Vector2D.sub(this.target, this.source);
        let len = Vector2D.norm(vec);
        let unit = Vector2D.unit(vec);
        let perp = new Vector2D(unit.y, -unit.x);
        let middle = Vector2D.add(this.source, Vector2D.scale(unit, len / 2));
        let result = Vector2D.add(
            middle,
            Vector2D.scale(perp, this.bend / (.66 * Math.PI))
        );
        return result;
    }

    protected preparePath(context: CanvasRenderingContext2D): void {
        context.beginPath();
        let vec = Vector2D.sub(this.target, this.source);
        let len = Vector2D.norm(vec);
        let unit = Vector2D.unit(vec);
        let perp = new Vector2D(unit.y, -unit.x);
        let middle = Vector2D.add(this.source, Vector2D.scale(unit, len / 2));

        let c = Vector2D.add(middle, Vector2D.scale(perp, this.bend));
        context.moveTo(this.source.x, this.source.y);
        context.quadraticCurveTo(c.x, c.y, this.target.x, this.target.y);
    }
}
