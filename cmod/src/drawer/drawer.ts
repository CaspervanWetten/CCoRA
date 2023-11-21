import DrawerOptions from "./drawer-options";

import Drawing from "src/drawing/drawing";
import { isSnappable } from "src/drawing/snappable-drawing";
import Vector2D from "src/vector/vector2d";

import Matrix from "src/matrix/matrix";
import { clamp } from "src/utils/math";

export default class Drawer {
    public context: CanvasRenderingContext2D;
    protected _currentTransform: Matrix;
    protected drawingCache: Drawing | null;
    protected _options: DrawerOptions;

    public constructor(canvas: HTMLCanvasElement, options?: DrawerOptions) {
        this.context = canvas.getContext("2d");
        this.drawingCache = null;
        this._currentTransform = Matrix.identity(3);

        if (options) {
            this.options = options;
        } else { // default settings
            this.options = {
                minZoom: 1,
                maxZoom: 1 / 10,
                width: 1500,
                height: 1500,
                gridOptions: {
                    drawGrid: true,
                    snapGrid: true,
                    horizontalGridSeperation: 50,
                    verticalGridSeperation:   50
                }
            };
        }

        this.resize();
        this.center();
    }

    public draw(drawing: Drawing | null = null): void {
        let canvas = this.context.canvas;
        if (!canvas) return;
        this.clear();
        if (this.options.gridOptions.drawGrid) {
            this.drawGrid();
        }
        let context = this.context;
        context!.save();
        if (!drawing) {
            drawing = this.drawingCache;
        }
        if (drawing) {
            if(this.options.gridOptions.snapGrid && isSnappable(drawing)) {
                drawing.snap(this.options.gridOptions.horizontalGridSeperation,
                             this.options.gridOptions.verticalGridSeperation,
                             context);
            }
            drawing.draw(context!);
            this.drawingCache = drawing;
        }

        context!.restore();
    }

    public clear(): void {
        let context = this.context;
        let canvas = context.canvas;
        context!.save();
        context!.setTransform(1, 0, 0, 1, 0, 0);
        let width = canvas.width;
        let height = canvas.height;
        context!.clearRect(0, 0, width, height);
        context!.restore();
    }

    public transform(mat: Matrix): void {
        let t = Matrix.mult(this.currentTransform, mat);
        this.setTransform(t);
    }

    public setTransform(mat: Matrix): void {
        mat = this.clampTransform(mat)
        this._currentTransform = mat;
        this.context.setTransform(mat.get(0,0), mat.get(1,0), mat.get(0,1),
                                  mat.get(1,1), mat.get(0,2), -mat.get(1,2));
        this.draw();
    }

    public shift(h: number = 0, v: number = 0): void {
        let mat = new Matrix(3, 3);
        mat.set(0, 2, h);
        mat.set(1, 2, v);
        let r = Matrix.add(mat, this.currentTransform);
        this.setTransform(r);
    }

    public zoom(amount: number): void {
        let zoom = Matrix.identity(3);
        zoom.set(0, 0, amount);
        zoom.set(1, 1, amount);
        let t = this.clampTransform(Matrix.mult(zoom, this.currentTransform));
        if (t.get(0, 0) == this.currentTransform.get(0, 0) &&
            t.get(1, 1) == this.currentTransform.get(1, 1)) {
            return;
        }

        let w  = this.context.canvas.width  / this.currentTransform.get(0, 0);
        let h  = this.context.canvas.height / this.currentTransform.get(1, 1);
        let vw = this.context.canvas.width  / t.get(0, 0);
        let vh = this.context.canvas.height / t.get(1, 1);
        let shift = new Matrix(3, 3);
        shift.set(0, 2,  (vw - w) / 2 * t.get(0, 0));
        shift.set(1, 2, -(vh - h) / 2 * t.get(1, 1));
        t = Matrix.add(t, shift);

        this.setTransform(t);
    }

    public resize(): void {
        let canvas = this.context.canvas;
        let parent = canvas.parentElement;
        if (parent) {
            canvas.width = parent.offsetWidth;
            canvas.height = parent.offsetHeight;
            if (canvas.width > this.options.width) {
                this.options.width = canvas.width;
            }
            if (canvas.height > this.options.height) {
                this.options.height = canvas.height;
            }
        }

        if(this.drawingCache) {
            this.draw(this.drawingCache);
        }

        this.setTransform(this.currentTransform);
    }

    public globalToLocal(event: MouseEvent): Vector2D {
        let canvas = this.context.canvas;
        let box = canvas.getBoundingClientRect();
        let x = event.clientX - Math.round(box.left);
        let y = event.clientY - Math.round(box.top);
        let context = this.context;
        let vector = new Matrix(3, 1);
        vector.set(0, 0, x);
        vector.set(1, 0, y);
        vector.set(2, 0, 1);
        let mat = this.currentTransform.inverse();
        let normalized = Matrix.mult(mat, vector);
        let result = new Vector2D(normalized.get(0,0), normalized.get(1,0));
        return result;
    }

    public localToGlobal(point: Vector2D): Vector2D {
        let x = point.x;
        let y = point.y;
        let t = this.currentTransform;
        x = (x * t.get(0, 0)) + t.get(0, 2);
        y = (y * t.get(1, 1)) - t.get(1, 2);
        return new Vector2D(x, y);
    }

    public get localCenter(): Vector2D {
        let canvas = this.context.canvas;
        let box = canvas.getBoundingClientRect();
        let width = box.width;
        let height = box.height;
        let t = this.currentTransform.inverse();
        let x = (width / 2) * t.get(0, 0) + t.get(0, 2);
        let y = (height / 2) * t.get(1, 1) + t.get(1, 2);
        return new Vector2D(x, y);
    }

    public center(): void {
        let t = this.currentTransform;
        let w = this.context.canvas.width;
        let h = this.context.canvas.height;
        t.set(0, 2,  w / 2);
        t.set(1, 2, -h / 2);
        this.setTransform(t);
    }

    protected clampTransform(mat: Matrix): Matrix {
        let options = this.options;
        let hscale = mat.get(0, 0);
        let vscale = mat.get(1, 1);
        mat.set(0, 0, clamp(hscale, options.minZoom, options.maxZoom));
        mat.set(1, 1, clamp(vscale, options.minZoom, options.maxZoom));

        hscale = mat.get(0, 0);
        vscale = mat.get(1, 1);

        let w = this.context.canvas.width;
        let h = this.context.canvas.height;

        let lhtrans = -(options.width / 2 - w / mat.get(0, 0)) * mat.get(0, 0); // right
        let hhtrans =  (options.width / 2) * mat.get(0, 0); // left

        let lvtrans = -(options.height / 2) * mat.get(1, 1); // top
        let hvtrans =  (options.height / 2 - h / mat.get(1, 1)) * mat.get(1, 1); // bottom

        let htrans = clamp(mat.get(0, 2), lhtrans, hhtrans);
        let vtrans = clamp(mat.get(1, 2), lvtrans, hvtrans);

        mat.set(0, 2, htrans);
        mat.set(1, 2, vtrans);
        return mat;
    }

    protected drawGrid(): void {
        let context = this.context;
        let canvas = context.canvas;
        let width = canvas.width;
        let height = canvas.height;

        let hdist = this.options.gridOptions.horizontalGridSeperation;
        let vdist = this.options.gridOptions.verticalGridSeperation;

        let transform = this.currentTransform;
        let hoffset = transform.get(0, 2);
        let voffset = transform.get(1, 2);

        let xmin = -(Math.ceil((hoffset / hdist) / transform.get(0, 0)) * hdist);
        let xmax = Math.ceil((width / transform.get(0,0)) / hdist) *
                   hdist + xmin + hdist;

        let ymin = Math.floor((voffset / vdist) / transform.get(1,1)) * vdist;
        let ymax = Math.ceil((height / transform.get(1,1)) / vdist) *
                   vdist + ymin + vdist;

        context.save();
        context.beginPath();
        context.strokeStyle = "rgb(220, 220, 220)";
        context.lineWidth = 1; // px
        for(let x = xmin; x <= xmax; x += hdist) {
            context.moveTo(x, ymin);
            context.lineTo(x, ymax);
        }
        for(let y = ymin; y <= ymax; y += vdist) {
            context.moveTo(xmin, y);
            context.lineTo(xmax, y);
        }
        context.closePath();
        context.stroke();
        context.restore();
    }

    public registerEvents(): void {
        window.addEventListener('resize', (event) => {
            this.resize();
        });
        // register key events
        let context = this.context;
        let canvas = context.canvas;
        let movementSpeed = 30;
        let zoomAmount = 1.2;
        canvas.addEventListener("keydown", (event) => {
            event.preventDefault();

            switch(event.key) {
                case "ArrowUp":
                    this.shift(0, -movementSpeed);
                    break;
                case "ArrowRight": 
                    this.shift(movementSpeed);
                    break;
                case "ArrowDown": 
                    this.shift(0, movementSpeed);
                    break;
                case "ArrowLeft":
                    this.shift(-movementSpeed);
                    break;
                case "+": 
                    this.zoom(zoomAmount);
                    break;
                case "=": 
                    this.zoom(zoomAmount);
                    break;
                case "-": 
                    this.zoom(1 / zoomAmount);
                    break;
                case "_": 
                    this.zoom(1 / zoomAmount);
                    break;
            }
        });

        // register mouse events
        let mouseDownMiddle = false;
        canvas.addEventListener("mousedown", (event) => {
            if (event.buttons === 1) {
                canvas.focus();
            }
            if (event.buttons === 4) {
                event.preventDefault();
                mouseDownMiddle = true;
            }
        });

        canvas.addEventListener("mousemove", (event) => {
            if(mouseDownMiddle) {
                let m = Matrix.identity(3);
                m.set(0, 2, event.movementX);
                m.set(1, 2, -event.movementY);
                this.transform(m);
            }
        });

        canvas.addEventListener("mouseup", (event) => {
            mouseDownMiddle = false;
        });

        canvas.addEventListener("wheel", (event) => {
            event.preventDefault();
        });
    }

    get currentTransform(): Matrix {
        return this._currentTransform;
    }

    get options(): DrawerOptions {
        return this._options;
    }

    set options(opts: DrawerOptions) {
        this._options = opts;
        this.draw();
    }
}
