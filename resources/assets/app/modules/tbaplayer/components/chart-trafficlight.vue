<template>
    <span :id="id">
        <img class="light" :src="imgsrcGreen" height="20" width="20">
        <img class="light" :src="imgsrcYellow" height="20" width="20">
        <img class="light" :src="imgsrcRed" height="20" width="20">
    </span>
</template>

<script>
import _    from 'lodash'
import Vuex from 'vuex'

export default {

    props: ['id','light'],

    data () {
        return {
            imgsrcGreenOff: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAAACXBIWXMAAAsSAAALEgHS3X78AAAA60lEQVQ4ja2U0QYCQRiFT6npbonYveqiN4noPSJKT5AUSbpOVr1HRERv0kVXLUvsXbv+2eyqaetvLDUfY9k557NjZrYQxzFMUDRiAVBibwB0oqVFIJsgqwSJdMTp80qQ3lZMgs8OW1o3WtUJ0iEQlOQlegzyd2J6yvbeltaL3AYAh30ip9YOx/WvokG0tpIAq+hxWuHIYiIAtraiR3Wyoqo2rkd1/t7+ZjisGBEdxeJmRPQkK7qy2XxUJyvyWCwf1VEit9xPjv2FRfX4BzEPmChhUx6ck4C2+uKyF7O3K/L1N2Lk0v6Kme0HcAfAkGoX+1/cDAAAAABJRU5ErkJggg==',
            imgsrcYellowOff: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAAACXBIWXMAAAsSAAALEgHS3X78AAAA5UlEQVQ4ja2UPw/BQByGX38SRDQkDZ0aMfkUtkoMBosPaTEYbVaTzSRiaqUJIQbS3k+qnPKrXHDP2HvfJ/endxkigg6yWiwA8uwLgHDmGAA1iKiGaMYkQBSCRLATIvCK3fnhvcOWFs4cG4AFEG5jryIIEYDCi1/qLVbJXvZN0oolSszTpG2niuLlwPxiW6zjuGkwEYAGi6qRnaSoxmJqZOfv49+P6gUtoupwe9YiepAU7dioGtlJijwWUyM7UpTrTKPf3mXRz/iVwfrARHfZJgp8rD5xy/3lyxVJfUa0XNpf0XP8AK4kXW8X5y96DgAAAABJRU5ErkJggg==',
            imgsrcRedOff: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAAACXBIWXMAAAsSAAALEgHS3X78AAAA30lEQVQ4ja2U0QoBQRiFzxpxt7WldpOIR5A8giSPK69Akkcgrqgt5UKoOaMlY/lNivnuZvacr5lmZwJjDHxQ8GIBUBQzAFjthaCJQUbQBEgYEtDcg9yp4+Tw3hFbY61fB5mA5iZ4E93naFJ1mi7zvZetsTFoAUjEEiUVXerWP4rYHIZZQFTcJFp1QiECEDsrbmwnL4qccTe28/fx66Bd9iJSZnH2InqQF+3F1+/YTl60E7Hv2I4VFVaj7LffiqibVOn5QYhug/V4kwWc1SdbdZm9XJGPz4iXS/srfo4fwBW4pmoXnzluGgAAAABJRU5ErkJggg==',
            imgsrcGreenOn: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAAACXBIWXMAAAsSAAALEgHS3X78AAACe0lEQVQ4jZ2UTUiUURSG33O++804Ouo4TiOiYAZOEhWm0c/WTUFoSkQEEWSriFbRslUQtSlatC/cBUIUKNJuMotIgggqnCh/CMk0f+bH+b5774nREktHq+dy4cI951mcy3tJRPAnnXdb6MDpVqecq102IVcD7PgiCPt+PjOj374Y1/ePD9q1betEt2fOu6JjpQG4VWBEAVQyU9Ba0SBaYMh331GzsF6678GQl7yQlN9EB+/tolNdR4KuF45NloW75xT3ZJhaDCyWt1iUGZOq8XV/Q94MiHDKJX/q2cSrbG/rE7squjVxucQNlNaOVkbvpB3uMDBYlfwU/TpHtP5wKIPrEDMC0WMDA08zy6Kri5dUPFsRT0XivVnm9pWG4qLCTY1v3+/P4aaIDJewHee2G3splouEpsLRrjyr9nWTL8IXF80TSk4AnMgbquDuo43ssK7IqkDPxi3FGQvgMJE0CzkxjtbtVhAV8Vi1Fe0owrSSaggaIDbGpIxDjLKNS7dmXqGeico5t2AJgPu/ooiBLwTFoQpVeH+9ruIvEcAHQXM2rQyATND47/5VUqMpA8EcLNKcdSe1iJ2v9HMP11VuwQ4Pn0EYJwczDnbmkWhqQljsfE65e5bYqRNsvWp9mU/kaRBMw4Yw6nx6NI030me1X+pX6aWPaeW25Jljm6nqPTu7L4d+AEkIvdbK+7qatdszV0pcE6glclq+BYInpwNu+5zimrURiWi9WO+ZVNy3zwG8BGHEYTP2eGh4JWsFCuk/13ksaHRom5BtJKImCG0HSRxAiABPBLOFmYhIqmj6CyQuJujstQ5VjvIy19qIiI3KZv/RwJCXPJMUAPgB6qN/wFWvEH4AAAAASUVORK5CYII=',
            imgsrcYellowOn: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAAACXBIWXMAAAsSAAALEgHS3X78AAACpUlEQVQ4EZ2TW0tUURiG32/tvd172uPM6ORgFEpBIRE11UXRVQcigroJ7EA3drjzJrqKfkHRXb8ggm6EYqCIiIyORAWdqLDUcjoomjrqHJzZh/WGVqYOofTAulrveljrY71CEvPJdGyQ3ds2m5HQsqhsS4uhICGtUPlhqRBku7/5q/bf0rOPzRGda0/JmdN7TNq1rq9r6hUkSTJOgS2QAGBeQUZDctSKcOL+9fve9hOv9BzRpUtrpW3vdjv0zAYODx6QcqGNXjmNqX1qkCFo2b2oTd5G/bKbhvAj4Q+87e8qprfc1TMiv6fdgWUsk4HPF8Ur7wOI6b3ZIh1A6wBw3I+qOX1eAc+VVPrePHxZ/CXKH7cqo27K7P98GX5l56+H/lvE0AOidR+s5vQFBXkM38say2Mdsqa5JVqTGzokk4X2qsn/lk6vaaEGGEKXxpfCckw4sawyOKxaDzQpk4jJZOFY1fkFCEeyWynSog1zqRmRJtOwjDgCb1NVcgF0/ke9AE1aM6lsmAZItyq12FuVcssFElWV0JkauFmVWCSGW+eRMJWNMqHE/18RQE9EAjVphSGpi6hx3ldlFkDFUkUAOZJ5Ven7EhhKxrgkdq0quQBmw6pPJLKiMKwynb3aQGWCsYYbjESfVqX/gUo0jku88QmALoMcNjL3RjD29YqOOJFAu4keVEobJfAb5s1hzoeUeGrEWLHuBgQPBHwtEQzNdK34+qRj17mNhKyXfO6gFMd2cbLQOKcijptnItWjovWPSXkmwhcw/Wzn1Se/uzbV/rNr5cjhHbaKm0lNWSmC1QCaRCQBsIZACKIggu8gugW6l8Dgu+/vSultnX/b/4fxb0ctVYotiThWgkAcVC4IAwoUoBxoPW4hyE3AK9zJPPJaT3URAH4CDetv6BMwFNMAAAAASUVORK5CYII=',
            imgsrcRedOn: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAAACXBIWXMAAAsSAAALEgHS3X78AAACU0lEQVQ4EZ2UT08TURTFz339R6eUtgitoaCSsCAxoS2JYVVYuhX2JP0IfgdduTB8A7t06YKwdsmfxIyLpihNSMQ2GZSWduiUOu275k41oZZq5KxeMu+e3Hfv7wwxM/6UOTensktLfkSjAQQCAWitoJSG67qwbdesVHrZWk3fLBsyeheN0rNcLohEYhLM0yC6B2AKQBDADwAtaH0BpeqwLLtYKrkF2+YhIzOdVtlMxoDPl+Jqf5Mb/W20eyvoa0BrsNYgQ1UoFdyjh8YutK6g0zk3y+VOtlrVnpHXST4fgd//QH/qvYbdewrNnsFNo99nigfK6kniBZg/oN3+Wjw8dAYdra+HEIvN68/6Ddr9vGfwFyP5RrPBklpNvITW+2g0akoGKzNhC5u45vzI5MeIrevHfOZsgWgRk5ORwXaYp7nF27eXjBefORsgWoLfH1feimU7XayMrRgjrneTIFqAUjHlcTJY8Z3Eje4CiAzlwTbg5E6ieNABsxoQO4DtrnJBpJWHvRAbpsr/GtFMqAmiJpgdJdkR7ClBeyM3/yF6FDkG8xcAl0oCKNmhGdqlmDJHbo8RpSYu6H74PYATMF8qL8WWZUt2aNH3iqbUye2lNzpJhy21mngL4ADMp2i12l5EitEoFdbWDBjGPIhy/L2/xefuBpq95FBoY74mpSeOKRmSTg4AfIRt13b297vD6V9eDsMwZj3shViBjTkGwP9rOzJYmYk85xRXV9+Kptkt1Os89D/yOstkApIdwV6IFdiEE1mxbEcGKzOR5+wcHfWeOw4DwE9M/D2kDyK0YwAAAABJRU5ErkJggg==',
            //初始值: Off
            imgsrcGreen: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAAACXBIWXMAAAsSAAALEgHS3X78AAAA60lEQVQ4ja2U0QYCQRiFT6npbonYveqiN4noPSJKT5AUSbpOVr1HRERv0kVXLUvsXbv+2eyqaetvLDUfY9k557NjZrYQxzFMUDRiAVBibwB0oqVFIJsgqwSJdMTp80qQ3lZMgs8OW1o3WtUJ0iEQlOQlegzyd2J6yvbeltaL3AYAh30ip9YOx/WvokG0tpIAq+hxWuHIYiIAtraiR3Wyoqo2rkd1/t7+ZjisGBEdxeJmRPQkK7qy2XxUJyvyWCwf1VEit9xPjv2FRfX4BzEPmChhUx6ck4C2+uKyF7O3K/L1N2Lk0v6Kme0HcAfAkGoX+1/cDAAAAABJRU5ErkJggg==',
            imgsrcYellow: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAAACXBIWXMAAAsSAAALEgHS3X78AAAA5UlEQVQ4ja2UPw/BQByGX38SRDQkDZ0aMfkUtkoMBosPaTEYbVaTzSRiaqUJIQbS3k+qnPKrXHDP2HvfJ/endxkigg6yWiwA8uwLgHDmGAA1iKiGaMYkQBSCRLATIvCK3fnhvcOWFs4cG4AFEG5jryIIEYDCi1/qLVbJXvZN0oolSszTpG2niuLlwPxiW6zjuGkwEYAGi6qRnaSoxmJqZOfv49+P6gUtoupwe9YiepAU7dioGtlJijwWUyM7UpTrTKPf3mXRz/iVwfrARHfZJgp8rD5xy/3lyxVJfUa0XNpf0XP8AK4kXW8X5y96DgAAAABJRU5ErkJggg==',
            imgsrcRed: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAAACXBIWXMAAAsSAAALEgHS3X78AAAA30lEQVQ4ja2U0QoBQRiFzxpxt7WldpOIR5A8giSPK69Akkcgrqgt5UKoOaMlY/lNivnuZvacr5lmZwJjDHxQ8GIBUBQzAFjthaCJQUbQBEgYEtDcg9yp4+Tw3hFbY61fB5mA5iZ4E93naFJ1mi7zvZetsTFoAUjEEiUVXerWP4rYHIZZQFTcJFp1QiECEDsrbmwnL4qccTe28/fx66Bd9iJSZnH2InqQF+3F1+/YTl60E7Hv2I4VFVaj7LffiqibVOn5QYhug/V4kwWc1SdbdZm9XJGPz4iXS/srfo4fwBW4pmoXnzluGgAAAABJRU5ErkJggg==',
        }
    },

    watch: {
        light: function(v) {
            switch(v) {
                case 'G':
                    this.imgsrcGreen = this.imgsrcGreenOn;
                    this.imgsrcYellow = this.imgsrcYellowOff;
                    this.imgsrcRed = this.imgsrcRedOff;
                    break;
                case 'Y':
                    this.imgsrcGreen = this.imgsrcGreenOff;
                    this.imgsrcYellow = this.imgsrcYellowOn;
                    this.imgsrcRed = this.imgsrcRedOff;
                    break;
                case 'R':
                    this.imgsrcGreen = this.imgsrcGreenOff;
                    this.imgsrcYellow = this.imgsrcYellowOff;
                    this.imgsrcRed = this.imgsrcRedOn;
                    break;
                default:
                    this.imgsrcGreen = this.imgsrcGreenOff;
                    this.imgsrcYellow = this.imgsrcYellowOff;
                    this.imgsrcRed = this.imgsrcRedOff;
            }
            
        }
    }

}
</script>

<style lang="scss" scoped>
</style>
