import { Module, VuexModule, Mutation } from "vuex-module-decorators";

import { MarkingStringType } from "src/system/marking";

@Module({
    name: "GraphDrawingSettingsModule",
    namespaced: true
})
export default class GraphDrawingSettingsModule extends VuexModule {
    _style: MarkingStringType = MarkingStringType.MINIMAL;

    get stringType(): MarkingStringType {
        return this._style;
    }

    @Mutation
    setType(st: MarkingStringType): void {
        this._style = st;
    }
}
