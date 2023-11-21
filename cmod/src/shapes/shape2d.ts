import Vector2D from "src/vector/vector2d";

export default interface Shape2D {
    stroke(ctx: CanvasRenderingContext2D): void;
    fill(ctx: CanvasRenderingContext2D): void;
    hit(point: Vector2D, ctx: CanvasRenderingContext2D): boolean;
}
