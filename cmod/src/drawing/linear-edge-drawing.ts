import Edge from "src/system/graph/edge";

import EdgeDrawing from "./edge-drawing";
import StateDrawing from "./state-drawing";

import Vector2D from "src/vector/vector2d";
import Arrow from "src/shapes/arrow";

import StyleManager from "src/style-manager/style-manager";

import { CanvasRenderingContext2DUtils } from "src/utils/canvas-rendering-context-2d";

export default class LinearEdgeDrawing extends EdgeDrawing {
    public readonly target: StateDrawing;
    protected _offset: number;

    protected validCache: boolean;
    protected arrowCache: Arrow | null;

    public constructor(edge: Edge, source: StateDrawing, target: StateDrawing) {
        super(edge, source);
        this.target = target;
        this.offset = 0;

        this.validCache = false;
        this.arrowCache = null;
    }

    public draw(context: CanvasRenderingContext2D): void {
        let arrow = this.getArrow(context);
        arrow.fill(context);
    }

    public drawText(context: CanvasRenderingContext2D): void {
        let point = this.getLabelPosition(context);
        let lwidth = context.measureText(this.edge.label).width + 5;
        let lheight = CanvasRenderingContext2DUtils.getFontSize(context) * 1.5;
        context.clearRect(point.x - lwidth / 2, point.y - lheight / 2,
                          lwidth, lheight);
        context.fillText(this.edge.label, point.x, point.y);
    }

    public hit(point: Vector2D, context: CanvasRenderingContext2D): boolean {
        context.save();
        context.setTransform(1, 0, 0, 1, 0, 0);
        let arrow = this.getArrow(context);
        let result = arrow.hit(point, context);
        context.restore();
        return result;
    }

    public getLabelPosition(context: CanvasRenderingContext2D): Vector2D {
        let arrow = this.getArrow(context);
        return arrow.getControlPoint();
    }

    protected getArrow(context: CanvasRenderingContext2D): Arrow {
        let arrow;
        if (!this.validCache || !this.arrowCache || !this.connectionsValid) {
            context.save();
            StyleManager.setStateStandardStyle(context);
            let c1 = this.source.center(context);
            let c2 = this.target.center(context);
            // determine start (source) point
            let intersection = this.source.getIntersection(c2, context);
            let p1 = Vector2D.add(c1,Vector2D.scale(intersection.vector, intersection.length));
            // determine end (target) point
            intersection = this.target.getIntersection(c1, context);
            let vec = Vector2D.scale(intersection.vector, intersection.length);
            let p2 = Vector2D.add(c2, vec);
            context.restore();
            arrow = new Arrow(p1.x, p1.y, p2.x, p2.y, this.offset);
            this.arrowCache = arrow;
            this.validCache = true;
            this.connectionsValid = true;
        } else {
            arrow = this.arrowCache;
        }
        return arrow;
    }

    public get offset(): number {
        return this._offset;
    }

    public set offset(o: number) {
        this._offset = o;
        this.validCache = false;
    }
}
