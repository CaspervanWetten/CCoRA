import GraphDrawingOptions from "./graph-drawing-options";

import Drawing           from "./drawing";
import Snappable         from "./snappable-drawing";
import EdgeDrawing       from "./edge-drawing";
import StateDrawing      from "./state-drawing";
import LinearEdgeDrawing from "./linear-edge-drawing";
import SelfLoopDrawing   from "./self-loop-drawing";

import Arrow    from "src/shapes/arrow";
import Vector2D from "src/vector/vector2d";

import { MarkingStringType } from "src/system/marking";
import StyleManager          from "src/style-manager/style-manager";

import HashTable from "src/hash-table/hash-table";
import HashSet   from "src/hash-set/hash-set";

export default class GraphDrawing implements Drawing, Snappable {
    public readonly states: HashTable<number, StateDrawing>;
    public readonly edges: HashTable<number, EdgeDrawing>;
    public initial: number | null;

    public options: GraphDrawingOptions;

    public constructor(options: GraphDrawingOptions | null = null) {
        this.states = new HashTable<number, StateDrawing>();
        this.edges = new HashTable<number, EdgeDrawing>();
        this.initial = null;
        if (options) {
            this.options = options;
        } else {
            this.options = {
                selected: null,
                feedback: null,
                markingStyle: MarkingStringType.FULL,
            };
        }
    }

    public draw(context: CanvasRenderingContext2D): void {
        let drawn = new HashSet<number>();
        let selected = this.options.selected;
        let feedback = this.options.feedback;

        // calculate bend for each edge
        let seperation = 80;
        let calc = new HashSet<number>(); // calculation cache
        this.states.each((sid: number, sdrawing: StateDrawing) => {
            sdrawing.markingStyle = this.options.markingStyle;
            this.states.each((osid: number, osdrawing: StateDrawing) => {
                let shared = this.edges.filterValues((edge: EdgeDrawing) => {
                    return edge instanceof LinearEdgeDrawing && (
                           edge.source == sdrawing && edge.target == osdrawing ||
                           edge.target == sdrawing && edge.source == osdrawing);
                });
                let k = 0;
                shared.each((eid: number, edge: LinearEdgeDrawing) => {
                    if (!calc.member(eid)) {
                        let c = 0;
                        if (shared.size > 1) {
                            c = (k * seperation) - ((seperation * (shared.size - 1)) / 2);
                        }
                        if (edge.source == sdrawing) {
                            c = -c;
                        }
                        edge.offset! = c;
                        k++;
                        calc.add(eid);
                    }
                });
            });
        });

        if (feedback !== null) {
            StyleManager.setStateStandardStyle(context);
            let cs, ws, is: Array<StateDrawing>;
            cs = new Array<StateDrawing>();
            ws = new Array<StateDrawing>();
            is = new Array<StateDrawing>();

            this.states.each((id: number, state: StateDrawing) => {
                let record = feedback.get(id);
                if (record !== null) {
                    if      (record.highest >= 400) is.push(state);
                    else if (record.highest >= 300) ws.push(state);
                    else if (record.highest >= 200) cs.push(state);
                }
            });

            StyleManager.setStyle(220, context);
            for (let i = 0; i < cs.length; i++) {
                cs[i].draw(context);
            }
            StyleManager.setStyle(320, context);
            for (let i = 0; i < ws.length; i++) {
                ws[i].draw(context);
            }
            StyleManager.setStyle(420, context);
            for (let i = 0; i < is.length; i++) {
                is[i].draw(context);
            }

            let ce, we, ie: Array<EdgeDrawing>;
            ce = new Array<EdgeDrawing>();
            we = new Array<EdgeDrawing>();
            ie = new Array<EdgeDrawing>();
            this.edges.each((id: number, edge: EdgeDrawing) => {
                let record = feedback.get(id);
                if (record !== null) {
                    if      (record.highest >= 400) ie.push(edge);
                    else if (record.highest >= 300) we.push(edge);
                    else if (record.highest >= 200) ce.push(edge);
                }
            });

            StyleManager.setStyle(240, context);
            for (let i = 0; i < ce.length; i++) {
                ce[i].draw(context);
            }
            StyleManager.setStyle(340, context);
            for (let i = 0; i < we.length; i++) {
                we[i].draw(context);
            }
            StyleManager.setStyle(440, context);
            for (let i = 0; i < ie.length; i++) {
                ie[i].draw(context);
            }
        }
        // draw selected element
        if (selected !== null) {
            if (this.hasState(selected)) {
                let drawing = this.getState(selected);
                StyleManager.setStateSelectedStyle(context);
                drawing.draw(context);
            } else if (this.hasEdge(selected)) {
                let drawing = this.getEdge(selected);
                StyleManager.setEdgeSelectedStyle(context);
                drawing.draw(context);
            }
        }
        // draw edges
        StyleManager.setEdgeStandardStyle(context);
        this.edges.each((id: number, edge: EdgeDrawing) => {
            edge.draw(context);
        });
        // draw states
        StyleManager.setStateStandardStyle(context);
        this.states.each((id: number, state: StateDrawing) => {
            state!.draw(context);
        });
        // draw text for states and edges
        StyleManager.setStateTextStyle(context);
        this.states.each((id: number, state: StateDrawing) => {
            state.drawText(context);
        });
        StyleManager.setEdgeTextStyle(context);
        this.edges.each((id: number, edge: EdgeDrawing) => {
            edge.drawText(context);
        });
        // draw initial state pointer
        if (this.initial != null) {
            context.fillStyle = "black";
            context.strokeStyle = "black";
            context.lineWidth = 2;
            let state = this.getState(this.initial);
            let pos = state!.position;
            let arrow = new Arrow(pos.x - 30, pos.y - 30, pos.x, pos.y);
            arrow.fill(context);
        }
    }

    public addState(id: number, drawing: StateDrawing): void {
        this.states.set(id, drawing);
    }

    public addEdge(id: number, edge: EdgeDrawing): void {
        this.edges.set(id, edge);
        let e = edge.edge;
        let source = this.getState(e.from);
        source.postset.push(edge);
        if (e.from !== e.to) {
            let target = this.getState(e.to);
            target.preset.push(edge);
        }
    }

    public delState(id: number): void {
        this.states.remove(id);
    }

    public delEdge(id: number): void {
        let edge = this.edges.get(id);
        if (edge instanceof LinearEdgeDrawing) {
            let index: number;
            index = edge.source.postset.indexOf(edge);
            edge.source.postset.splice(index, 1);
            index = edge.target.preset.indexOf(edge);
            edge.target.preset.splice(index, 1);
        }
        this.edges.remove(id);
    }

    public getDrawing(id: number): Drawing {
        if(this.hasState(id)) {
            return this.getState(id);
        } 
        if(this.hasEdge(id)) {
            return this.getEdge(id);
        }
        return null;
    }

    public getState(id: number): StateDrawing {
        if(this.hasState(id)) {
            return this.states.get(id)!;
        }
        return null;
    }

    public getEdge(id: number): EdgeDrawing {
        if (this.hasEdge(id)) {
            return this.edges.get(id)!;
        }
        return null;
    }

    public hasState(id: number): boolean {
        return this.states.has(id);
    }

    public hasEdge(id: number): boolean {
        return this.edges.has(id);
    }

    public getDrawingAt(pos: Vector2D, context: CanvasRenderingContext2D): number | null {
        let result = null;
        this.states.each((id: number, state: StateDrawing) => {
            if (state.hit(pos, context)) {
                result = id;
                return;
            }
        });
        if (result !== null) return result;
        this.edges.each((id: number, edge: EdgeDrawing) => {
            if (edge.hit(pos, context)) {
                result = id;
                return;
            }
        });
        return result;
    }

    public snap(hgrid: number, vgrid: number, context: CanvasRenderingContext2D): void {
        context.save();
        StyleManager.setStateStandardStyle(context);
        this.states.each((id: number, state: StateDrawing) => {
            state.snap(hgrid, vgrid, context);
        });
        context.restore();
    }
}
