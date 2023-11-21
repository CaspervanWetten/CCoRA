import Shape2D from "./shape2d";
import Vector2D from "src/vector/vector2d";

export default class Circle implements Shape2D {
    public readonly center: Vector2D;
    public readonly radius: number;
    public readonly rotation: number;

    public constructor(x: number, y:number, r: number, rot: number = 2 * Math.PI) {
        this.center = new Vector2D(x, y);
        this.radius = r;
        this.rotation = rot;
    }

    public stroke(context: CanvasRenderingContext2D): void {
        this.preparePath(context);
        context.stroke();
    }

    public fill(context: CanvasRenderingContext2D): void {
        this.preparePath(context);
        context.fill();
    }

    public hit(pos: Vector2D, context: CanvasRenderingContext2D): boolean {
        context.save();
        context.lineWidth = 20;
        this.preparePath(context);
        let result = context.isPointInPath(pos.x, pos.y);
        context.restore();
        return result;
    }

    protected preparePath(context: CanvasRenderingContext2D): void {
        context.beginPath();
        context.arc(this.center.x,
                    this.center.y,
                    this.radius,
                    0,
                    this.rotation
        );
    }
}
