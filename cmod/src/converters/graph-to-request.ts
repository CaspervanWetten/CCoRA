import Graph from "src/system/graph/graph";
import Edge from "src/system/graph/edge";
import State from "src/system/graph/state";
import { StateRequest, EdgeRequest, InitialRequest, GraphRequest } from "src/types";

export default class GraphToRequest {
    public static convert(g: Graph) {
        let states = g.states;
        let edges = g.edges;
        
        let requestStates = new Array<StateRequest>();
        let requestEdges = new Array<EdgeRequest>();

        states.each((id: number, state: State) => {
            let r: StateRequest = {
                state: state.toString(),
                id: id
            };
            requestStates.push(r);
        });

        edges.each((id: number, edge: Edge) => {
            let r: EdgeRequest = {
                fromId: edge.from,
                toId: edge.to,
                transition: edge.label,
                id: id
            };
            requestEdges.push(r);
        });

        let initial = g.initial;
        let requestInitial: InitialRequest = {
            id: initial
        }

        let result: GraphRequest = {
            states: requestStates,
            edges: requestEdges,
            initial: requestInitial
        };
        return result;
    }
}
