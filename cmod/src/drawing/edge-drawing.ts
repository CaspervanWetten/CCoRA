import StateDrawing from "./state-drawing";
import Hittable from "./hittable-drawing";

import Edge from "src/system/graph/edge";

import Vector2D from "src/vector/vector2d";

export default abstract class EdgeDrawing implements Hittable {
    public source: StateDrawing;
    public edge: Edge;

    public connectionsValid: boolean;

    public constructor(edge: Edge, source: StateDrawing) {
        this.source = source;
        this.edge = edge;
        this.connectionsValid = false;
    }

    public abstract draw(context:CanvasRenderingContext2D): void;

    public abstract drawText(context: CanvasRenderingContext2D): void;

    public abstract hit(point: Vector2D, context: CanvasRenderingContext2D): boolean;

    public abstract getLabelPosition(context: CanvasRenderingContext2D): Vector2D;
}
