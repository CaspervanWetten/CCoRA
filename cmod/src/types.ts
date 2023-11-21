// Request types
interface InitialRequest {
    id: number | null;
}

interface EdgeRequest {
    id: number;
    fromId: number;
    toId: number;
    transition: string;
}

interface StateRequest {
    state: string;
    id: number;
}

interface GraphRequest {
    states: Array<StateRequest>;
    edges: Array<EdgeRequest>;
    initial: InitialRequest;
}

// Response types
interface UserResponse {
    id: string;
    name: string;
    created_on: string;
}

interface UserListResponse {
    next_page: string;
    prev_page: string;
    users: UserResponse[];
}

interface UserCreatedResponse {
    user_id: string;
}

interface MarkedPetrinetResponse {
    petrinet: PetrinetResponse;
    marking: Object;
}

interface PetrinetResponse {
    places: Array<string>;
    transitions: Array<string>;
    flows: Array<FlowResponse>;
}

interface FlowResponse {
    from: string;
    to: string;
    weight: number;
}

type MarkingResponse = Object;

interface PetrinetCreatedResponse {
    petrinet_id: number;
    marking_id: number;
}

interface SessionCreatedResponse {
    session_id: number;
}

interface FeedbackResponse {
    general: Array<number>;
    specific: Array<number>;
}

type ErrorResponse = string;

export {
    InitialRequest,
    EdgeRequest,
    StateRequest,
    GraphRequest,
    UserResponse,
    UserListResponse,
    UserCreatedResponse,
    MarkedPetrinetResponse,
    PetrinetResponse,
    MarkingResponse,
    PetrinetCreatedResponse,
    SessionCreatedResponse,
    FeedbackResponse,
    ErrorResponse
};
