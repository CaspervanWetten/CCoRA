import Vector2D from "src/vector/vector2d";
import Line from "./line";
import Triangle from "./triangle";

export default class Arrow extends Line {
    public readonly width: number;
    public readonly height: number;

    public constructor(
        x1: number, 
        y1: number, 
        x2: number, 
        y2: number, 
        b: number = 0,  // bend
        w: number = 10, // tip width
        h: number = 15  // tip height
        ) {
        super(x1, y1, x2, y2, b);
        this.width = w;
        this.height = h;
    }

    public stroke(context: CanvasRenderingContext2D): void {
        context.save();
        super.stroke(context);
        let tip = this.getTip();
        tip.stroke(context);
        context.restore();
    }

    public fill(context: CanvasRenderingContext2D): void {
        context.save();
        super.fill(context);
        let tip = this.getTip();
        tip.fill(context);
        context.restore();
    }

    protected getTip(): Triangle {
        let a = new Vector2D(this.target.x, this.target.y);
        let rev = Vector2D.unit(Vector2D.sub(this.getControlPoint(), a));
        let perp = new Vector2D(rev.y, -rev.x);
        let d = Vector2D.add(this.target, Vector2D.scale(rev, this.height));
        let b = Vector2D.add(d, Vector2D.scale(perp, this.width / 2));
        let c = Vector2D.sub(d, Vector2D.scale(perp, this.width / 2));
        let triangle = new Triangle(a.x, a.y,
                                    b.x, b.y,
                                    c.x, c.y);
        return triangle;
    }
}
