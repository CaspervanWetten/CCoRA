import Shape2D from "./shape2d";
import Vector2D from "src/vector/vector2d";

export default class Triangle implements Shape2D {
    public readonly alpha: Vector2D;
    public readonly beta: Vector2D;
    public readonly gamma: Vector2D;

    public constructor(
        x1: number, y1: number,
        x2: number, y2: number,
        x3: number, y3: number
        ) {
        this.alpha = new Vector2D(x1, y1);
        this.beta = new Vector2D(x2, y2);
        this.gamma = new Vector2D(x3, y3);
    }

    public stroke(context: CanvasRenderingContext2D): void {
        this.preparePath(context);
        context.stroke();
    }

    public fill(context: CanvasRenderingContext2D): void {
        this.preparePath(context);
        context.fill();
    }

    public hit(point: Vector2D, context: CanvasRenderingContext2D): boolean {
        return false;
    }

    protected preparePath(context:CanvasRenderingContext2D): void {
        context.beginPath();
        context.moveTo(this.alpha.x, this.alpha.y);
        context.lineTo(this.beta.x , this.beta.y);
        context.lineTo(this.gamma.x, this.gamma.y);
        context.closePath();
    }
}
